<?php

namespace App\Services\Apilo;

use App\DTO\ApiloResult;

class ApiloService
{
    public function __construct(private ApiloClient $client) {}

    public function fetchProductBySku(string $sku): ApiloResult
    {
        $response = $this->client->get('rest/api/warehouse/product', ['sku' => $sku]);

        if (!$response->successful()) {
            return ApiloResult::fail("Produkt $sku nie znaleziony");
        }

        $product = $response->json('products.0');

        return $product ? ApiloResult::ok($product, 'Pobrano produkt') : ApiloResult::fail("Brak produktu dla SKU $sku");
    }

    public function updateStockQuantity(string $sku, int $amount): ApiloResult
    {
        $productResponse = $this->fetchProductBySku($sku);

        if (!$productResponse->success) {
            return ApiloResult::fail($productResponse->message);
        }

        $product = $productResponse->data;

        $newQty = $this->calculateNewQuantity($product, $amount);

        $payload = $this->buildPayload($product, $sku, $newQty);

        $response = $this->client->put('rest/api/warehouse/product/', $payload);

        if (! $response->successful()) {
            return ApiloResult::fail("Błąd " . $response->status() . ": " . $response->json('message'));
        }

        return ApiloResult::ok($response->json());
    }

    private function calculateNewQuantity(array $product, int $amount): int
    {
        return ((int) ($product['quantity'] ?? 0)) - $amount;
    }

    private function buildPayload(array $product, string $sku, int $newQty): array
    {
        return [
            [
                'id' => (int) $product['id'],
                'sku' => $sku,
                'quantity' => $newQty,
                'tax' => (int) ($product['tax'] ?? 0),
                'status' => (int) ($product['status'] ?? 1),
                'priceWithTax' => $product['priceWithTax'] ?? null,
            ]
        ];
    }
}
