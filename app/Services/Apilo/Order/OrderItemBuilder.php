<?php

namespace App\Services\Apilo\Order;

use App\DTO\OrderItemsResult;
use Illuminate\Support\Collection;
use Throwable;

class OrderItemBuilder
{
    public function build(Collection $products, float $discount, float $vat): OrderItemsResult
    {
        $items = [];
        $errors = [];
        $totalNet = 0.0;
        $totalGross = 0.0;

        foreach ($products as $decision) {
            try {
                $product = $decision->product ?? [];
                $csv = $decision->csv;

                $qty = (int) $csv->quantity;

                $sku = trim($csv->sku ?? '');
                $name = trim($product['name'] ?? $csv->name ?? '');
            } catch (Throwable $e) {
                $errors[] = "Błąd przetwarzania produktu: {$e->getMessage()}";

                continue;
            }

            if ($sku === '') {
                $errors[] = "Brak SKU dla produktu: {$name}";

                continue;
            }

            if ($qty <= 0) {
                $errors[] = "Nieprawidłowa ilość dla produktu: {$name} (SKU: {$sku})";

                continue;
            }

            $netAfterDiscount = round($csv->netto * (1 - $discount), 2);

            $vatValue = round($netAfterDiscount * $vat, 2);
            $gross = $netAfterDiscount + $vatValue;

            $totalNet += $netAfterDiscount * $qty;
            $totalGross += $gross * $qty;

            $items[] = [
                'id' => $product['id'] ?? null,
                'ean' => $product['ean'] ?? null,
                'originalCode' => $product['originalCode'] ?? null,
                'sku' => $sku,
                'originalName' => $name,
                'originalPriceWithoutTax' => $netAfterDiscount,
                'originalPriceWithTax' => $gross,
                'quantity' => $qty,
                'tax' => round($vat * 100, 2),
                'type' => '1',
                'unit' => $product['unit'] ?? 'szt.',
            ];
        }

        return new OrderItemsResult($items, $totalNet, $totalGross, $errors);
    }
}
