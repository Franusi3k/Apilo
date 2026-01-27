<?php

namespace Tests\Unit;

use App\DTO\CsvClientData;
use App\DTO\OrderItemsResult;
use App\DTO\StockCheckSummary;
use App\Services\Apilo\ApiloClient;
use App\Services\Apilo\Order\OrderItemBuilder;
use App\Services\Apilo\Order\OrderPayloadFactory;
use App\Services\Apilo\Order\OrderService;
use App\Services\Apilo\StockCheckService;
use App\Services\PreviewService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Client\Response;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    private function makeService(
        $preview,
        $apilo,
        $stock,
        $builder,
        $factory
    ): OrderService {
        return new OrderService($preview, $apilo, $stock, $builder, $factory);
    }

    public function test_send_order_returns_success_when_everything_is_ok(): void
    {
        $preview = Mockery::mock(PreviewService::class);
        $apilo = Mockery::mock(ApiloClient::class);
        $stock = Mockery::mock(StockCheckService::class);
        $builder = Mockery::mock(OrderItemBuilder::class);
        $factory = Mockery::mock(OrderPayloadFactory::class);

        $products = collect([
            ['sku' => 'SKU1', 'qty' => 1],
        ]);

        $preview->shouldReceive('parseCsv')
            ->once()
            ->andReturn($products);

        $preview->shouldReceive('extractClientData')
            ->once()
            ->andReturn(new CsvClientData(
                name: 'John',
                company: 'Company 1',
                street: 'Street',
                streetNumber: '10',
                postcode: '00-000',
                city: 'Warsaw',
                country: 'PL',
                phone: '123-123-123',
            ));

        $stock->shouldReceive('processProductsWithStockCheck')
            ->once()
            ->andReturn(new StockCheckSummary(
                confirmed: $products->toArray(),
                pending: [],
                notFound: [],
            ));

        $builder->shouldReceive('build')
            ->once()
            ->with(Mockery::type('Illuminate\Support\Collection'), 0.10, 0.23)
            ->andReturn(new OrderItemsResult(
                items: [['sku' => 'SKU1', 'qty' => 1]],
                totalNet: 100.0,
                totalGross: 123.0,
                errors: [],
            ));

        $factory->shouldReceive('make')
            ->once()
            ->andReturn(['payload']);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->once()->andReturn(true);

        $apilo->shouldReceive('post')
            ->once()
            ->with('rest/api/orders/', ['payload'])
            ->andReturn($response);

        $service = new OrderService($preview, $apilo, $stock, $builder, $factory);

        $result = $service->sendOrder(
            ['deliveryMethod' => 'Eurohermes', 'discount' => 10, 'vat' => 23],
            UploadedFile::fake()->create('test.csv'),
            'Notes',
            Request::create('/api/send', 'POST', [
                'ignore_missing_sku' => false,
                'confirmed_only' => false,
                'ignore_low_stock' => false,
            ])
        );

        $this->assertTrue($result->success);
        $this->assertSame('Zamówienie zostało wysłane pomyślnie', $result->message);
    }

    public function test_send_order_returns_error_when_delivery_method_is_not_supported(): void
    {
        $preview = Mockery::mock(PreviewService::class);
        $apilo = Mockery::mock(ApiloClient::class);
        $stock = Mockery::mock(StockCheckService::class);
        $builder = Mockery::mock(OrderItemBuilder::class);
        $factory = Mockery::mock(OrderPayloadFactory::class);

        $products = collect([['sku' => 'SKU1', 'qty' => 1]]);

        $preview->shouldReceive('parseCsv')->andReturn($products);
        $preview->shouldReceive('extractClientData')->andReturn(new CsvClientData(
            name: 'John',
            company: 'Company 1',
            street: 'Street',
            streetNumber: '10',
            postcode: '00-000',
            city: 'Warsaw',
            country: 'PL',
            phone: '123-123-123',
        ));

        $stock->shouldReceive('processProductsWithStockCheck')->andReturn(
            new StockCheckSummary($products->toArray(), [], [])
        );

        $builder->shouldReceive('build')->andReturn(
            new OrderItemsResult(
                items: [['sku' => 'SKU1', 'qty' => 1]],
                totalNet: 100,
                totalGross: 123,
                errors: []
            )
        );

        $service = $this->makeService($preview, $apilo, $stock, $builder, $factory);

        $result = $service->sendOrder(
            ['deliveryMethod' => 'UNKNOWN'],
            UploadedFile::fake()->create('test.csv')
        );

        $this->assertFalse($result->success);
        $this->assertSame('Nieobsługiwana metoda dostawy', $result->message);
    }

    public function test_send_order_returns_warning_when_sku_is_not_found(): void
    {
        $preview = Mockery::mock(PreviewService::class);
        $stock = Mockery::mock(StockCheckService::class);

        $preview->shouldReceive('parseCsv')->andReturn(collect([
            ['sku' => 'BADSKU', 'qty' => 1],
        ]));

        $preview->shouldReceive('extractClientData')->andReturn(new CsvClientData(
            name: 'John',
            company: 'Company',
            street: 'Street',
            streetNumber: '10',
            postcode: '00-000',
            city: 'Warsaw',
            country: 'PL',
            phone: '123-123-123',
        ));

        $stock->shouldReceive('processProductsWithStockCheck')->andReturn(
            new StockCheckSummary(
                confirmed: [],
                pending: [],
                notFound: [
                    (object)['csv' => (object)['sku' => 'BADSKU']]
                ]
            )
        );

        $service = new OrderService(
            $preview,
            Mockery::mock(ApiloClient::class),
            $stock,
            Mockery::mock(OrderItemBuilder::class),
            Mockery::mock(OrderPayloadFactory::class)
        );

        $result = $service->sendOrder(
            ['deliveryMethod' => 'Eurohermes'],
            UploadedFile::fake()->create('test.csv'),
            null,
            Request::create('/', 'POST', [
                'ignore_missing_sku' => false,
            ])
        );

        $this->assertFalse($result->success);
        $this->assertSame('Nie znaleziono części produktów po SKU.', $result->message);
        $this->assertContains('BADSKU', $result->data['notFound']);
    }
}
