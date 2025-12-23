<?php

namespace App\DTO;

readonly class StockCheckSummary
{
    public function __construct(
        public array $confirmed,
        public array $pending,
        public array $notFound,
    ) {}
}
