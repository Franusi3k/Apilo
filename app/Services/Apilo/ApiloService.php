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
}
