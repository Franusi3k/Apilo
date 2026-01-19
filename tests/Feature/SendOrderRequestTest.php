<?php

namespace Tests\Feature;

use App\DTO\OrderResult;
use App\Services\Apilo\Order\OrderService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SendOrderRequestTest extends TestCase
{
    public function test_request_with_valid_payload_passes_validation(): void
    {
        $file = UploadedFile::fake()->create('transaction3000.csv');

        $payload = [
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
                ->andReturn(
                    OrderResult::success('OK')
                );
        });

        $response = $this->postJson('/api/send', $payload);

        $response->assertStatus(200);
        $response->assertJsonMissing(['errors']);
    }

    public function test_request_without_file_returns_error_and_422_status(): void
    {
        $payload = [
            'generalData' => [
                'client' => 'klient1',
                'phone' => '123-123-123',
                'vat' => '50',
                'discount' => 5,
                'deliveryMethod' => 'DPD',
                'taxNumber' => 'PL300',
            ],
            'notes' => '',
        ];

        $response = $this->postJson('/api/send', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function test_request_without_generalData_returns_error_and_422_status(): void
    {
        $response = $this->postJson('/api/send', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['generalData']);
    }

    public function test_request_without_client_returns_error_and_422_status(): void
    {
        $payload = [
            'generalData' => [
                'phone' => '123-123-123',
                'vat' => '50',
                'discount' => 5,
                'deliveryMethod' => 'DPD',
                'taxNumber' => 'PL300',
            ],
            'notes' => '',
        ];

        $response = $this->postJson('/api/send', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['generalData.client']);
    }

    public function test_request_with_invalid_file_format_returns_error_and_422_status(): void
    {
        $file = UploadedFile::fake()->create('file.py');

        $payload = [
            'generalData' => [
                'client' => null,
                'phone' => '123-123-123',
                'vat' => '50',
                'discount' => 5,
                'deliveryMethod' => 'DPD',
                'taxNumber' => 'PL300',
            ],
            'notes' => '',
            'file' => $file,
        ];

        $response = $this->postJson('/api/send', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function test_request_with_vat_out_of_range_returns_422(): void
    {
        $file = UploadedFile::fake()->create('transaction.csv');

        $payload = [
            'generalData' => [
                'client' => 'klient1',
                'phone' => '123-123-123',
                'vat' => 150,
                'discount' => 5,
                'deliveryMethod' => 'DPD',
                'taxNumber' => 'PL300',
            ],
            'notes' => '',
            'file' => $file,
        ];

        $response = $this->postJson('/api/send', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['generalData.vat']);
    }
}
