<?php

namespace App\Services\Apilo\Order;

use App\DTO\OrderResult;
use App\Services\Apilo\ApiloClient;
use App\Services\Apilo\Order\CarrierAccountResolver;
use App\Services\Apilo\Order\OrderItemBuilder;
use App\Services\Apilo\Order\OrderPayloadFactory;
use App\Services\Apilo\StockCheckService;
use App\Services\PreviewService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OrderService
{
    public function __construct(
        private readonly PreviewService $previewService,
        private readonly ApiloClient $apiloClient,
        private readonly StockCheckService $stockCheckService,
        private readonly OrderItemBuilder $itemBuilder,
        private readonly OrderPayloadFactory $payloadFactory,
    ) {}

    public function sendOrder(array $generalData, UploadedFile $file, ?string $notes = null, ?Request $request = null): OrderResult
    {
        try {
            $products = $this->previewService->parseCsv($file);

            $clientData = $this->previewService->extractClientData($products);

            $stockResult = $this->handleStockCheck(
                $products->toArray(),
                $request?->boolean('ignore_missing_sku') ?? false,
                $request?->boolean('confirmed_only') ?? false,
                $request?->boolean('ignore_low_stock') ?? false
            );

            if (! $stockResult->success) {
                return $stockResult;
            }

            $products = collect($stockResult->data);

            $discount = (float) ($generalData['discount'] ?? 0.00) / 100.0;
            $vat = (float) ($generalData['vat'] ?? 0.00) / 100.0;

            $itemsResult = $this->itemBuilder->build($products, $discount, $vat);

            if (empty($itemsResult->items)) {
                return OrderResult::error('Brak prawidłowych produktów w pliku', $itemsResult->errors);
            }

            $carrierAccountId = CarrierAccountResolver::resolve($generalData['deliveryMethod']);

            if (! $carrierAccountId) {
                return OrderResult::error('Nieobsługiwana metoda dostawy', []);
            }

            $orderedAt = now()->format('Y-m-d\TH:i:sO');
            $notes = $notes ?: 'Brak notatki';

            $payload = $this->payloadFactory->make(
                $generalData,
                $clientData,
                $itemsResult->items,
                $itemsResult->totalNet,
                $itemsResult->totalGross,
                $carrierAccountId,
                $notes,
                $orderedAt
            );

            $response = $this->apiloClient->post('rest/api/orders/', $payload);

            if (! $response->successful()) {
                return OrderResult::error('Błąd podczas wysyłania zamówienia');
            }

            return OrderResult::success('Zamówienie zostało wysłane pomyślnie');
        } catch (Exception $ex) {
            return OrderResult::error('Niespodziewany błąd: ' . $ex->getMessage());
        }
    }

    private function handleStockCheck(array $products, bool $ignoreMissingSku, bool $confirmedOnly, bool $ignoreLowStock): OrderResult
    {
        $stockCheck = $this->stockCheckService->processProductsWithStockCheck($products);

        $confirmed = $stockCheck->confirmed;
        $toConfirm = $stockCheck->pending;
        $notFound = $stockCheck->notFound;

        if (! empty($notFound) && ! $ignoreMissingSku) {
            return OrderResult::warning('Nie znaleziono części produktów po SKU.', ['notFound' => array_map(
                fn ($line) => $line->csv->sku,
                $notFound
            )]);
        }

        if (! empty($toConfirm)) {
            if ($confirmedOnly) {
                $products = $confirmed;
            } elseif ($ignoreLowStock) {
                $products = array_merge($confirmed, $toConfirm);
            } else {
                return OrderResult::warning(
                    'Niektóre produkty mają zbyt mały stan magazynowy.',
                    [
                        'missingProducts' => array_map(
                            fn ($line) => $line->toApiArray(),
                            $toConfirm
                        ),
                        'confirmedProducts' => array_map(
                            fn ($line) => $line->toApiArray(),
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
