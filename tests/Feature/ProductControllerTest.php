<?php

namespace Tests\Feature;

use App\DTO\ApiloResult;
use App\Services\Apilo\ApiloService;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function test_request_with_valid_sku_returns_200_and_product(): void
    {
        $this->mock(ApiloService::class, function ($mock) {
            $mock->shouldReceive('fetchProductBySku')
                ->once()
                ->andReturn(
                    ApiloResult::ok([], 'Pobrano produkt')
                );
        });

        $response = $this->getJson('api/product/TEST-SKU-123');

        $response->assertStatus(200);
        $response->assertJsonStructure(['product' => []]);
    }

    public function test_request_with_invalid_sku_returns_404_and_error_message(): void
    {
        $this->mock(ApiloService::class, function ($mock) {
            $mock->shouldReceive('fetchProductBySku')
            ->once()
            ->andReturn(
                ApiloResult::fail('Brak produktu dla SKU TEST-SKU-1234')
            );
        });

        $response = $this->getJson('api/product/TEST-SKU-1234');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Brak produktu dla SKU TEST-SKU-1234']);
    }
}
