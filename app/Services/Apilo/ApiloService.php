<?php

namespace App\Services\Apilo;

use App\DTO\ApiloResult;

class ApiloService
{
    public function __construct(private readonly ApiloClient $client) {}

    public function fetchProductBySku(string $sku): ApiloResult
    {
        $response = $this->client->get('rest/api/warehouse/product', ['sku' => $sku]);

        if (! $response->successful()) {
            return ApiloResult::fail("Produkt {$sku} nie znaleziony");
        }

        $product = $response->json('products.0');

        return $product ? ApiloResult::ok($product, 'Pobrano produkt') : ApiloResult::fail("Brak produktu dla SKU {$sku}");
    }

    public function updateStockQuantities(array $payload): ApiloResult
    {
        if ($payload === []) {
            return ApiloResult::ok([]);
        }

        $response = $this->client->put('rest/api/warehouse/product/', $payload);

        if (! $response->successful()) {
            return ApiloResult::fail('Błąd ' . $response->status() . ': ' . $response->json('message'));
        }

        return ApiloResult::ok($response->json());
    }

    public function createStockPayloadItem(array $product, string $sku, int $amount): array
    {
        $newQty = $this->calculateNewQuantity($product, $amount);

        return $this->buildPayloadItem($product, $sku, $newQty);
    }

    private function calculateNewQuantity(array $product, int $amount): int
    {
        return ((int) ($product['quantity'] ?? 0)) - $amount;
    }

    private function buildPayloadItem(array $product, string $sku, int $newQty): array
    {
        return [
            'id' => (int) $product['id'],
            'sku' => $sku,
            'quantity' => $newQty,
            'tax' => (int) ($product['tax'] ?? 0),
            'status' => (int) ($product['status'] ?? 1),
            'priceWithTax' => $product['priceWithTax'] ?? null,
        ];
    }
}
