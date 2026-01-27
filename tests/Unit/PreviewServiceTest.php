<?php

namespace Tests\Unit;

use App\DTO\CsvClientData;
use App\DTO\CsvOrderLine;
use App\Services\PreviewService;
use App\Services\Csv\CsvReader;
use App\Services\Csv\CsvOrderRowMapper;
use App\Services\Csv\CsvClientExtractor;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class PreviewServiceTest extends TestCase
{
    public function test_parse_csv_reads_and_maps_rows(): void
    {
        $reader = Mockery::mock(CsvReader::class);
        $mapper = Mockery::mock(CsvOrderRowMapper::class);
        $extractor = Mockery::mock(CsvClientExtractor::class);

        $rawRows = collect([
            ['raw' => 1],
            ['raw' => 2],
        ]);

        $mappedRow1 = new CsvOrderLine(name: 'Product 1', quantity: 2, price: 10.0, sku: 'SKU1',  netto: 12.3, currency: 'PLN', ean: "xd", client: []);
        $mappedRow2 = new CsvOrderLine(name: 'Product 2', quantity: 1, price: 5.0, sku: 'SKU2', netto: 6.15, currency: 'PLN', ean: "xd", client: []);

        $reader->shouldReceive('read')
            ->once()
            ->with('file.csv')
            ->andReturn($rawRows);

        $mapper->shouldReceive('map')
            ->once()
            ->with(['raw' => 1])
            ->andReturn($mappedRow1);

        $mapper->shouldReceive('map')
            ->once()
            ->with(['raw' => 2])
            ->andReturn($mappedRow2);

        $service = new PreviewService($reader, $mapper, $extractor);

        $result = $service->parseCsv('file.csv');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($mappedRow1, $result->first());
        $this->assertSame($mappedRow2, $result->last());
    }

    public function test_extract_client_data_uses_first_row_client(): void
    {
        $reader = Mockery::mock(CsvReader::class);
        $mapper = Mockery::mock(CsvOrderRowMapper::class);
        $extractor = Mockery::mock(CsvClientExtractor::class);

        $clientArray = ['name' => 'John'];


        $rows = collect([
            (object) ['client' => $clientArray],
            (object) ['client' => ['name' => 'Ignored']],
        ]);

        $clientData = new CsvClientData(
            name: 'John',
            company: 'Company',
            street: 'Street',
            streetNumber: '10',
            postcode: '00-000',
            city: 'Warsaw',
            country: 'PL',
            phone: '123-123-123',
        );

        $extractor->shouldReceive('extract')
            ->once()
            ->with($clientArray)
            ->andReturn($clientData);

        $service = new PreviewService($reader, $mapper, $extractor);

        $result = $service->extractClientData($rows);

        $this->assertSame($clientData, $result);
    }
}
