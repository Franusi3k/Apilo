<?php

#here will be logic to process order

namespace App\Services\Apilo;

use App\Services\PreviewService;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Collection;

class OrderService
{
    protected PreviewService $previewService;
    protected ApiloService $apiloService;
    protected ApiloClient $apiloClient;
    protected StockCheckService $stockCheckService;

    public function __construct(
        PreviewService $previewService,
        ApiloService $apiloService,
        ApiloClient $apiloClient,
        StockCheckService $stockCheckService
    ) {
        $this->previewService = $previewService;
        $this->apiloService = $apiloService;
        $this->apiloClient = $apiloClient;
        $this->stockCheckService = $stockCheckService;
    }

    public function sendOrder(
        $generalDataJson,
        $invoiceDataJson,
        $shippingDataJson,
        $file,
        $notes = null,
        $request = null
        ): array
    {
        try {
            $generalData = json_decode($generalDataJson, true);
            $invoiceData = json_decode($invoiceDataJson, true);
            $shippingData = json_decode($shippingDataJson, true);

            $products = $this->previewService->parseCsv($file);

            $stockResult = $this->handleStockCheck(
                $products->toArray(),
                $request->boolean('ignore_missing_sku') ?? false,
                $request->boolean('confirmed_only') ?? false,
                $request->boolean('ignore_low_stock' ?? false)
            );
            if ($stockResult['status'] !== 'success') {
                return $stockResult;
            }

            $products = collect($stockResult['products']);

            $discount = (float) ($generalData['discount'] ?? 0.00) / 100.0;
            $vat = (float) ($generalData['vat'] ?? 0.00) / 100.0;
            $deliveryCost = (float) ($generalData['deliveryCost'] ?? 0.00);

            [$items, $totalNet, $totalGross, $errors] = $this->prepareItems($products, $discount, $vat);

            if (empty($items)) {
                return [
                    'status' => 'error',
                    'message' => 'Brak prawidłowych produktów w pliku',
                    'data' => $errors,
                    'status_code' => 422,
                ];
            }

            $invoiceStreetFull = $invoiceData['street'] ?? '';
            [$invoiceStreetName, $invoiceStreetNumber] = splitStreetAndNumber($invoiceStreetFull);

            $shippingStreetFull = $shippingData['street'] ?? '';
            [$shippingStreetName, $shippingStreetNumber] = splitStreetAndNumber($shippingStreetFull);

            $CARRIER_MAP = [
                'Eurohermes' => 9,
                'RohligSuus' => 69,
            ];

            $carrierAccountId = $CARRIER_MAP[$generalData['deliveryMethod']] ?? null;

            if (!$carrierAccountId) {
                return [
                    'status' => 'error',
                    'message' => 'Nieobsługiwana metoda dostawy',
                    'data' => [],
                    'status_code' => 400,
                ];
            }

            $totalGross += $deliveryCost;
            $orderedAt = now()->format('Y-m-d\TH:i:sO');
            $notes = $notes ?: 'Brak notatki';

            $payload = $this->buildPayload(
                $generalData,
                $invoiceData,
                $shippingData,
                $items,
                $totalNet,
                $totalGross,
                $invoiceStreetName,
                $invoiceStreetNumber,
                $shippingStreetName,
                $shippingStreetNumber,
                $carrierAccountId,
                $notes,
                $orderedAt
            );

            $response = Http::withHeaders($this->apiloClient->headers())
                ->post(config('apilo.base_url') . 'rest/api/orders/', $payload);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => 'Pomyślnie wysłano zamówienie',
                    'data' => $response->json(),
                    'status_code' => $response->status(),
                ];
            }
            return [
                'status' => 'error',
                'message' => 'Błąd podczas wysyłania zamówienia: ' . $response->body(),
                'data' => [],
                'status_code' => $response->status(),
            ];
        } catch (Exception $ex) {
            return [
                'status' => 'error',
                'message' => 'Niespodziewany błąd: ' . $ex->getMessage(),
                'data' => [],
                'status_code' => 500,
            ];
        }
    }

    private function prepareItems(Collection $products, float $discount, float $vat): array
    {
        $items = [];
        $errors = [];
        $totalNet = 0.0;
        $totalGross = 0.0;

        foreach ($products as $row) {
            try {
                $qty = (int) ($row['quantity'] ?? 0);
                $netPriceStr = $row['netto'] ?? '0';
                $netPrice = parsePrice($netPriceStr);
                $sku = trim($row['sku'] ?? '');
                $name = trim($row['name'] ?? '');

                if (empty($sku)) {
                    $errors[] = "Brak SKU dla produktu: {$name}";
                    continue;
                }

                if ($qty <= 0) {
                    $errors[] = "Nieprawidłowa ilość dla produktu: {$name} (SKU: {$sku})";
                    continue;
                }

                $productResponse = $this->apiloService->fetchProductBySku($sku);

                if ($productResponse['status'] !== 'success') {
                    $errors[] = "Produkt nie znaleziony dla SKU: {$sku}";
                    continue;
                }

                $product = $productResponse['data'];

                $discountedNet = $netPrice * (1 - $discount);
                $discountedGross = $discountedNet * (1 + $vat);

                $totalNet += $discountedNet * $qty;
                $totalGross += $discountedGross * $qty;

                $items[] = [
                    'id' => $product['id'] ?? '',
                    'ean' => $product['ean'] ?? '',
                    'originalCode' => $product['originalCode'] ?? '',
                    'sku' => $sku,
                    'originalName' => $name,
                    'originalPriceWithTax' => round($discountedGross, 2),
                    'originalPriceWithoutTax' => round($discountedNet, 2),
                    'quantity' => $qty,
                    'tax' => number_format($vat * 100, 2),
                    'type' => '1',
                    'unit' => $product['unit'] ?? 'szt.',
                ];
            } catch (Exception $e) {
                $errors[] = "Błąd przetwarzania produktu: {$e->getMessage()}";
            }
        }

        return [$items, $totalNet, $totalGross, $errors];
    }

    private function buildPayload(
        array $generalData,
        array $invoiceData,
        array $shippingData,
        array $items,
        float $totalNet,
        float $totalGross,
        string $invoiceStreetName,
        string $invoiceStreetNumber,
        string $shippingStreetName,
        string $shippingStreetNumber,
        int $carrierAccountId,
        string $notes,
        string $orderedAt
    ): array {
        return [
            "platformId" => config('apilo.platform_id'),
            "idExternal" => 'WWW/' . now()->format('YmdHis'),
            "isInvoice" => true,
            "customerLogin" => $generalData['client'] ?? 'unknown',
            "paymentStatus" => 2,
            "paymentType" => 1,
            "originalCurrency" => "PLN",
            "originalAmountTotalWithoutTax" => round($totalNet, 2),
            "originalAmountTotalWithTax" => round($totalGross, 2),
            "originalAmountTotalPaid" => round($totalGross, 2),
            "preferences" => [],
            "orderItems" => $items,
            "addressCustomer" => [
                "name" => $invoiceData['name'] ?? '',
                "phone" => $generalData['phone'] ?? '',
                "email" => "shipping@zentrada.com",
                "streetName" => $invoiceStreetName,
                "streetNumber" => $invoiceStreetNumber,
                "city" => $invoiceData['city'] ?? '',
                "zipCode" => $invoiceData['postcode'] ?? '',
                "country" => $invoiceData['country'] ?? '',
                "companyTaxNumber" => $invoiceData['taxNumber'] ?? '',
                "companyName" => $invoiceData['company'] ?? '',
            ],
            "addressDelivery" => [
                "name" => $shippingData['name'] ?? '',
                "phone" => $generalData['phone'] ?? '',
                "email" => "shipping@zentrada.com",
                "streetName" => $shippingStreetName,
                "streetNumber" => $shippingStreetNumber,
                "city" => $shippingData['city'] ?? '',
                "zipCode" => $shippingData['postcode'] ?? '',
                "country" => $shippingData['country'] ?? '',
                "companyName" => $shippingData['company'] ?? '',
            ],
            "addressInvoice" => [
                "name" => $invoiceData['name'] ?? '',
                "phone" => $generalData['phone'] ?? '',
                "email" => "shipping@zentrada.com",
                "streetName" => $invoiceStreetName,
                "streetNumber" => $invoiceStreetNumber,
                "city" => $invoiceData['city'] ?? '',
                "zipCode" => $invoiceData['postcode'] ?? '',
                "country" => $invoiceData['country'] ?? '',
                "companyName" => $invoiceData['company'] ?? '',
            ],
            "carrierAccount" => $carrierAccountId,
            "orderNotes" => [
                [
                    "type" => "1",
                    "comment" => $notes,
                ]
            ],
            "orderedAt" => $orderedAt,
            "status" => 42,
        ];
    }

    private function handleStockCheck(array $products, $ignoreMissingSku, $confirmedOnly, $ignoreLowStock): array
    {
        $stockCheck = $this->stockCheckService->processProductsWithStockCheck($products);

        $confirmed = $stockCheck['confirmed'];
        $toConfirm = $stockCheck['toConfirm'];
        $notFound = $stockCheck['notFound'];

        if (!empty($notFound) && !$ignoreMissingSku) {
            return [
                'status' => 'warning',
                'message' => 'Nie znaleziono części produktów po SKU.',
                'data' => ['notFound' => $notFound],
                'status_code' => 409,
            ];
        }

        if (!empty($toConfirm)) {
            if ($confirmedOnly) {
                $products = $confirmed;
            } elseif ($ignoreLowStock) {
                $products = array_merge($confirmed, $toConfirm);
            } else {
                return [
                    'status' => 'warning',
                    'message' => 'Niektóre produkty mają zbyt mały stan magazynowy.',
                    'data' => [
                        'missingProducts' => $toConfirm,
                        'confirmedProducts' => $confirmed,
                    ],
                    'status_code' => 409,
                ];
            }
        } else {
            $products = $confirmed;
        }

        return [
            'status' => 'success',
            'products' => $products,
        ];
    }
}
