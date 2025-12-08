<?php

namespace App\DTO;

use App\Enums\StockStatus;

class StockLineResult
{
    public function __construct(
        public readonly StockStatus $status,
        public readonly ?array $product,
        public readonly array $csv,
        public readonly ?int $requestedQuantity = null,
        public readonly ?int $missingQuantity = null,
        public readonly ?string $reason = null
    ) {}
}
