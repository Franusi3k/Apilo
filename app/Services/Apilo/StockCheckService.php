<?php

namespace App\Services\Apilo;

class StockCheckService
{
    protected ApiloService $apiloService;

    public function __construct(ApiloService $apiloService)
    {
        $this->apiloService = $apiloService;
    }

    /**
     * Sprawdza dostępność produktów po SKU i dzieli je na 3 grupy:
     * - confirmed (dostępne)
     * - pending (za mało na stanie)
     * - notFound (brak lub błąd)
     */
    public function processProductsWithStockCheck(array $csvData): array
    {
        $confirmedProducts = [];
        $pendingConfirmation = [];
        $notFound = [];

        foreach ($csvData as $row) {
            $sku = $row['sku'] ?? null;
            $requestedQuantity = (int) ($row['quantity'] ?? 0);

            if (empty($sku)) {
                $notFound[] = [
                    'sku' => null,
                    'reason' => 'Brak SKU w pliku',
                ];
                continue;
            }

            $productResponse = $this->apiloService->fetchProductBySku($sku);

            if ($productResponse['status'] !== 'success' || empty($productResponse['data'])) {
                $notFound[] = [
                    'sku' => $sku,
                    'reason' => $productResponse['message'] ?? 'Produkt nie znaleziony',
                ];
                continue;
            }

            $product = $productResponse['data'];

            try {
                $currentStock = (int) ($product['quantity'] ?? 0);
            } catch (\Exception $e) {
                $notFound[] = [
                    'sku' => $sku,
                    'reason' => 'Nieprawidłowa ilość na stanie',
                ];
                continue;
            }

            $combinedData = [
                'product' => $product,
                'requested_quantity' => $requestedQuantity,
                'csv_data' => $row,
            ];

            if ($currentStock >= $requestedQuantity) {
                $confirmedProducts[] = $combinedData;
            } else {
                $combinedData['missing_quantity'] = $requestedQuantity - $currentStock;
                $pendingConfirmation[] = $combinedData;
            }
        }

        return [
            'confirmed' => $confirmedProducts,
            'toConfirm' => $pendingConfirmation,
            'notFound' => $notFound,
        ];
    }
}
