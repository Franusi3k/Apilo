<?php

namespace App\DTO;

class StockCheckResult
{
    public function __construct(
        public readonly array $confirmed,
        public readonly array $pending,
        public readonly array $notFound,
    ) {}
}
