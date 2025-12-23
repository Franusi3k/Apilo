<?php

namespace App\DTO;

use App\Enums\StockStatus;

readonly class StockDecision
{
    public function __construct(
        public StockStatus $status,
        public ?array $product,
        public CsvOrderLine $csv,
        public ?int $requestedQuantity = null,
        public ?int $missingQuantity = null,
        public ?string $reason = null
    ) {}

    public function toApiArray(): array
    {
        return [
            'name' => $this->product['name'] ?? null,
            'sku' => $this->csv->sku,
            'requested' => $this->requestedQuantity,
            'missing' => $this->missingQuantity,
            'status' => $this->status->value,
        ];
    }
}
