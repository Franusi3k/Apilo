<?php

namespace App\Services\Apilo;

use App\DTO\OrderResult;
use App\Services\PreviewService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OrderService
{

    public function __construct(
        protected PreviewService $previewService,
        protected ApiloService $apiloService,
        protected ApiloClient $apiloClient,
        protected StockCheckService $stockCheckService
    ) {}

    public function sendOrder(
        $generalDataJson,
        $file,
        $notes = null,
        $request = null
    ): OrderResult {

        try {
            //dane z formularza
            $generalData = json_decode($generalDataJson, true);

            //produlty z pliku
            $products = $this->previewService->parseCsv($file);

            //dane klienta
            $clientData = $this->previewService->extractClientData($products);

            $stockResult = $this->handleStockCheck(
                $products->toArray(),
                $request->boolean('ignore_missing_sku') ?? false,
                $request->boolean('confirmed_only') ?? false,
                $request->boolean('ignore_low_stock') ?? false
            );

            if (! $stockResult->success) {
                return $stockResult;
            }

            $products = collect($stockResult->data);

            $discount = (float) ($generalData['discount'] ?? 0.00) / 100.0;
            $vat = (float) ($generalData['vat'] ?? 0.00) / 100.0;

            [$items, $totalNet, $totalGross, $errors] = $this->prepareItems($products, $discount, $vat);

            if (empty($items)) {
                return OrderResult::error('Brak prawidłowych produktów w pliku', $errors);
            }
            
            // zmienicić to na pobieranie z API
            $CARRIER_MAP = [
                'Eurohermes' => 9,
                'RohligSuus' => 69,
            ];

            $carrierAccountId = $CARRIER_MAP[$generalData['deliveryMethod']] ?? null;

            if (! $carrierAccountId) {
                return OrderResult::error('Nieobsługiwana metoda dostawy', []);
            }

            $orderedAt = now()->format('Y-m-d\TH:i:sO');
            $notes = $notes ?: 'Brak notatki';

            $payload = $this->buildPayload(
                $generalData,
                $clientData,
                $items,
                $totalNet,
                $totalGross,
                $carrierAccountId,
                $notes,
                $orderedAt
            );

            $response = Http::withHeaders($this->apiloClient->headers())
                ->post(config('apilo.base_url') . 'rest/api/orders/', $payload);

            foreach ($items as $item) {
                $sku = $item['sku'];
                $qty = (int) $item['quantity'];
                if ($sku && $qty > 0) {
                    $this->apiloService->updateStockQuantity($item['sku'], $item['quantity']);
                }
            }

            if ($response->successful()) {
                return OrderResult::success('Pomyślnie wysłano zamówienie', $response->json());
            }

            return OrderResult::error('Błąd podczas wysyłania zamówienia: ' . $response->body());
        } catch (Exception $ex) {
            return OrderResult::error('Niespodziewany błąd: ' . $ex->getMessage());
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
                $product = $row->product ?? [];
                $csv = $row->csv;

                $qty = (int)$csv->quantity;
                $netPrice = parsePrice($csv->netto);

                $sku = trim($csv->sku ?? '');
                $name = trim($product['name'] ?? $csv->name ?? '');

            } catch (\Throwable $e) {
                $errors[] = "Błąd przetwarzania produktu: {$e->getMessage()}";
                continue;
            }

            if ($sku === '') {
                $errors[] = "Brak SKU dla produktu: $name";
                continue;
            }

            if ($qty <= 0) {
                $errors[] = "Nieprawidłowa ilość dla produktu: $name (SKU: $sku)";
                continue;
            }

            $discountedNet = $netPrice * (1 - $discount);
            $discountedGross = $discountedNet * (1 + $vat);

            $totalNet += $discountedNet * $qty;
            $totalGross += $discountedGross * $qty;

            $items[] = [
                'id' => $product['id'] ?? null,
                'ean' => $product['ean'] ?? null,
                'originalCode' => $product['originalCode'] ?? null,
                'sku' => $sku,
                'originalName' => $name,
                'originalPriceWithTax' => round($discountedGross, 2),
                'originalPriceWithoutTax' => round($discountedNet, 2),
                'quantity' => $qty,
                'tax' => number_format($vat * 100, 2),
                'type' => '1',
                'unit' => $product['unit'] ?? 'szt.',
            ];
        }
        return [$items, $totalNet, $totalGross, $errors];
    }

    private function buildPayload(
        array $generalData,
        array $clientData,
        array $items,
        float $totalNet,
        float $totalGross,
        int $carrierAccountId,
        string $notes,
        string $orderedAt
    ): array {
        return [
            'platformId' => config('apilo.platform_id'),
            'idExternal' => 'WWW/' . now()->format('YmdHis'),
            'isInvoice' => true,
            'customerLogin' => $generalData['client'] ?? 'unknown',
            'paymentStatus' => 2,
            'paymentType' => 1,
            'originalCurrency' => 'PLN',
            'originalAmountTotalWithoutTax' => round($totalNet, 2),
            'originalAmountTotalWithTax' => round($totalGross, 2),
            'originalAmountTotalPaid' => round($totalGross, 2),
            'preferences' => [],
            'orderItems' => $items,
            'addressCustomer' => [
                'name' => $generalData['client'] ?? '',
                'phone' => $clientData['phone'] ?? '',
                'email' => 'shipping@zentrada.com',
                'streetName' => $clientData['street'],
                'streetNumber' => $clientData['streetNumber'],
                'city' => $clientData['city'] ?? '',
                'zipCode' => $clientData['postcode'] ?? '',
                'country' => $clientData['country'] ?? '',
                'companyTaxNumber' => $generalData['taxNumber'] ?? '', // do zmiany
                'companyName' => $clientData['company'] ?? '',
            ],
            'addressDelivery' => [
                'name' => $clientData['name'] ?? '',
                'phone' => $clientData['phone'] ?? '',
                'email' => 'shipping@zentrada.com',
                'streetName' => $clientData['street'],
                'streetNumber' => $clientData['streetNumber'],
                'city' => $clientData['city'] ?? '',
                'zipCode' => $clientData['postcode'] ?? '',
                'country' => $clientData['country'] ?? '',
                'companyName' => $clientData['company'] ?? '',
            ],
            'addressInvoice' => [
                'name' => $clientData['name'] ?? '',
                'phone' => $clientData['phone'] ?? '',
                'email' => 'shipping@zentrada.com',
                'streetName' => $clientData['street'],
                'streetNumber' => $clientData['streetNumber'],
                'city' => $clientData['city'] ?? '',
                'zipCode' => $clientData['postcode'] ?? '',
                'country' => $clientData['country'] ?? '',
                'companyName' => $clientData['company'] ?? '',
            ],
            'carrierAccount' => $carrierAccountId,
            'orderNotes' => [
                [
                    'type' => '1',
                    'comment' => $notes,
                ],
            ],
            'orderedAt' => $orderedAt,
            'status' => 42,
        ];
    }

    private function handleStockCheck(array $products, $ignoreMissingSku, $confirmedOnly, $ignoreLowStock): OrderResult
    {
        $stockCheck = $this->stockCheckService->processProductsWithStockCheck($products);

        $confirmed = $stockCheck->confirmed;
        $toConfirm = $stockCheck->pending;
        $notFound  = $stockCheck->notFound;

        if (! empty($notFound) && ! $ignoreMissingSku) {
            return OrderResult::warning('Nie znaleziono części produktów po SKU.', ['notFound' => array_map(
                fn($line) => $line->csv->sku,
                $notFound
            )]);
        }

        if (! empty($toConfirm)) {
            if ($confirmedOnly) {
                $products = $confirmed;
            } elseif ($ignoreLowStock) {
                $products = array_merge($confirmed, $toConfirm);
            } else {
                return OrderResult::warning('Niektóre produkty mają zbyt mały stan magazynowy.', [
                        'missingProducts' => array_map(
                            fn($line) => $line->toApiArray(),
                            $toConfirm
                        ),
                        'confirmedProducts' => array_map(
                            fn($line) => $line->toApiArray(),
                            $confirmed
                        ),
                    ],
                );
            }
        } else {
            $products = $confirmed;
        }

        return OrderResult::success('', $products);
    }
}
