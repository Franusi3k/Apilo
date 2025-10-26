<?php

namespace App\Services\Apilo;

use Illuminate\Support\Facades\Http;

class ApiloService
{
    protected ApiloClient $client;

    public function __construct(ApiloClient $client)
    {
        $this->client = $client;
    }

    public function fetchProductBySku(string $sku): array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($this->client->headers())
                ->get(config('apilo.base_url').'rest/api/warehouse/product', ['sku' => $sku]);

            if ($response->status() !== 200) {
                return [
                    'status' => 'error',
                    'message' => "Produkt $sku nie znaleziony: ".$response->body(),
                    'data' => null,
                ];
            }

            $data = $response->json();
            $products = $data['products'] ?? [];

            if (empty($products)) {
                return [
                    'status' => 'error',
                    'message' => 'Brak produktu dla SKU '.$sku,
                    'data' => null,
                ];
            }

            return ['status' => 'success', 'message' => 'Pomyślnie pobrano produkt na podstawie SKU', 'data' => $products[0]];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => "Exception while fetching {$sku}: ".$e->getMessage(),
                'data' => null,
            ];
        }
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
            ->put(config('apilo.base_url').'/warehouse/product/', $payload);

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
