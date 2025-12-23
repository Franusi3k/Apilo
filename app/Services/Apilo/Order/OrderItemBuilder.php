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
                $netPrice = parsePrice($csv->netto);

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

        return new OrderItemsResult($items, $totalNet, $totalGross, $errors);
    }
}
