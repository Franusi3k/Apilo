<?php

namespace App\DTO;

use App\Enums\StockStatus;

class StockDecision
{
    public function __construct(
        public readonly StockStatus $status,
        public readonly ?array $product,
        public readonly CsvOrderLine $csv,
        public readonly ?int $requestedQuantity = null,
        public readonly ?int $missingQuantity = null,
        public readonly ?string $reason = null
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
