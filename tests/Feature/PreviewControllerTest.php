<?php

namespace Tests\Feature;

use App\Services\PreviewService;
use Exception;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PreviewControllerTest extends TestCase
{
    public function test_request_without_file_returns_400(): void
    {
        $response = $this->post('/preview');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Brak pliku']);
    }

    public function test_preview_returns_500_when_service_throws_exception(): void
    {
        $this->mock(PreviewService::class, function ($mock) {
            $mock->shouldReceive('parseCsv')
                ->once()
                ->andThrow(new Exception('Boom'));
        });

        $file = UploadedFile::fake()->create('test.csv');

        $response = $this->post('/preview', [
            'excel_file' => $file
        ]);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Błąd przetwarzania pliku: Boom']);
    }

    public function test_preview_returns_200_and_records_when_file_present(): void
    {
        $records = collect([
            ['id' => 1, 'name' => 'Test record'],
        ]);

        $this->mock(PreviewService::class, function ($mock) use ($records) {
            $mock->shouldReceive('parseCsv')
                ->once()
                ->andReturn($records);
        });

        $file = UploadedFile::fake()->create('test.csv');

        $response = $this->post('/preview', [
            'excel_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            ['id' => 1, 'name' => 'Test record'],
        ]);
    }
}
