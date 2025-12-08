<?php

namespace App\Services\Apilo;

use App\DTO\ApiloResult;
use Illuminate\Support\Facades\Http;

class ApiloService
{
    public function __construct(private ApiloClient $client) {}

    public function fetchProductBySku(string $sku): ApiloResult
    {
        $response = Http::withHeaders($this->client->headers())
            ->get(config('apilo.base_url') . 'rest/api/warehouse/product', ['sku' => $sku]);

        if (!$response->successful()) {
            return ApiloResult::fail("Produkt $sku nie znaleziony");
        }

        $product = $response->json('products.0');

        return $product ? ApiloResult::ok($product, 'Pobrano produkt') : ApiloResult::fail("Brak produktu dla SKU $sku");
    }

    public function updateStockQuantity(string $sku, int $delta): array
    {
        $productResponse = $this->fetchProductBySku($sku);

        if ($productResponse['status'] !== 'success' || empty($productResponse['data'])) {
            return [
                'status' => 'error',
                'message' => "Nie znaleziono produktu: {$sku}",
                'data' => null,
            ];
        }

        $product = $productResponse['data'];

        try {
            $currentQty = (int) ($product['quantity'] ?? 0);
            $newQty = $currentQty - $delta;
        } catch (\Throwable $e) {
            return [null, "Błąd podczas przeliczania ilości: {$e->getMessage()}"];
        }

        $productId = $product['id'] ?? null;
        if (! $productId) {
            return [null, "Brak ID produktu dla SKU {$sku}"];
        }

        $taxStr = $product['tax'] ?? '0';
        try {
            $tax = (int) floatval($taxStr);
        } catch (\Throwable $e) {
            $tax = 0;
        }

        $payload = [[
            'id' => (int) $productId,
            'sku' => $sku,
            'quantity' => $newQty,
            'tax' => $tax,
            'status' => (int) ($product['status'] ?? 1),
            'priceWithTax' => $product['priceWithTax'] ?? null,
        ]];

        $response = Http::withHeaders($this->client->headers())
            ->put(config('apilo.base_url') . '/warehouse/product/', $payload);

        if (! $response->successful()) {
            return [null, "Błąd {$response->status()}: {$response->body()}"];
        }

        try {
            return [$response->json(), null];
        } catch (\Throwable $e) {
            return [null, "Błąd dekodowania JSON: {$e->getMessage()}"];
        }
    }
}
