<?php

namespace App\Services\Apilo;

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
                ->get(config('apilo.base_url') . 'rest/api/warehouse/product', ['sku' => $sku]);

            if ($response->status() !== 200) {
                return [null, "Produkt $sku nie znaleziony: " . $response->body()];
            }

            $data = $response->json();
            $products = $data['products'] ?? [];

            if (empty($products)) {
                return [null, "Brak produktu dla SKU $sku"];
            }

            return [$products[0], null];

        } catch (\Exception $e) {
            return [null, "Wyjątek dla $sku: " . $e->getMessage()];
        }
    }
}
