<?php

namespace Tests\Feature;

use App\DTO\OrderResult;
use App\Services\Apilo\Order\OrderService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    public function test_order_returns_200_when_request_is_valid(): void
    {
        $file = UploadedFile::fake()->create('transaction3000.csv');

        $data = [
            'generalData' => [
                'client' => 'klient1',
                'phone' => '123-123-123',
                'vat' => '50',
                'discount' => 5,
                'deliveryMethod' => 'DPD',
                'taxNumber' => 'PL300',
            ],
            'notes' => '',
            'file' => $file,
        ];

        $this->mock(OrderService::class, function ($mock) {
            $mock->shouldReceive('sendOrder')
                ->once()
                ->andReturn(
                    OrderResult::success('Zamówienie zostało wysłane pomyślnie')
                );
        });

        $response = $this->post('/api/send', $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Zamówienie zostało wysłane pomyślnie']);
    }

    public function test_order_returns_custom_http_status_from_service(): void
    {
        $file = UploadedFile::fake()->create('transaction.csv');

        $data = [
            'generalData' => [
                'client' => 'klient1',
                'phone' => '123-123-123',
                'vat' => '50',
                'discount' => 5,
                'deliveryMethod' => 'DPD',
                'taxNumber' => 'PL300',
            ],
            'notes' => 'Testowe zamówienie',
            'file' => $file,
        ];

        $this->mock(OrderService::class, function ($mock) {
            $mock->shouldReceive('sendOrder')
                ->once()
                ->andReturn(
                    OrderResult::warning(
                        'Nieprawidłowe dane zamówienia',
                    )
                );
        });

        $response = $this->post('/api/send', $data);

        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'Nieprawidłowe dane zamówienia',
        ]);
    }
}
