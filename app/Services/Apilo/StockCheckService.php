<?php

namespace App\Services\Apilo;

use App\DTO\CsvOrderLine;
use App\DTO\StockCheckSummary;
use App\DTO\StockDecision;
use App\Enums\StockStatus;

/**
 * Sprawdza dostępność produktów po SKU i dzieli je na 3 grupy:
 * - confirmed (dostępne)
 * - pending (za mało na stanie)
 * - notFound (brak lub błąd)
 */

class StockCheckService
{
    public function __construct(private ApiloService $apiloService) {}

    public function processProductsWithStockCheck(array $csvData): StockCheckSummary
    {
        $confirmed = [];
        $pending = [];
        $notFound = [];

        foreach ($csvData as $row) {

            $line = $this->processLine($row);

            match ($line->status) {
                StockStatus::CONFIRMED => $confirmed[] = $line,
                StockStatus::PENDING   => $pending[]   = $line,
                StockStatus::NOT_FOUND => $notFound[]  = $line,
            };
        }

        return new StockCheckSummary($confirmed, $pending, $notFound);
    }

    private function processLine(CsvOrderLine $row): StockDecision
    {
        $sku = $row->sku;
        $requested = (int) $row->quantity;

        if (!$sku) {
            return new StockDecision(
                status: StockStatus::NOT_FOUND,
                product: null,
                csv: $row,
                reason: 'Brak SKU w pliku'
            );
        }

        $productResponse = $this->apiloService->fetchProductBySku($sku);

        if (!$productResponse->success) {
            return new StockDecision(
                status: StockStatus::NOT_FOUND,
                product: null,
                csv: $row,
                reason: $productResponse->message
            );
        }

        $product = $productResponse->data;
        $stock = (int) ($product['quantity'] ?? 0);

        if ($stock >= $requested) {
            return new StockDecision(
                status: StockStatus::CONFIRMED,
                product: $product,
                csv: $row,
                requestedQuantity: $requested
            );
        }

        return new StockDecision(
            status: StockStatus::PENDING,
            product: $product,
            csv: $row,
            requestedQuantity: $requested,
            missingQuantity: $requested - $stock
        );
    }
}