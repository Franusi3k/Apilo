<?php

namespace App\DTO;

class StockCheckSummary
{
    public function __construct(
        public readonly array $confirmed,
        public readonly array $pending,
        public readonly array $notFound,
    ) {}
}
