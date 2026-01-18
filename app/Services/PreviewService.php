<?php

namespace App\Services;

use App\DTO\CsvClientData;
use App\Services\Csv\CsvClientExtractor;
use App\Services\Csv\CsvOrderRowMapper;
use App\Services\Csv\CsvReader;
use Illuminate\Support\Collection;

class PreviewService
{
    public function __construct(
        private CsvReader $reader,
        private CsvOrderRowMapper $mapper,
        private CsvClientExtractor $clientExtractor,
    ) {}

    public function parseCsv($file): Collection
    {
        return $this->reader
            ->read($file)
            ->map(fn ($row) => $this->mapper->map($row));
    }

    public function extractClientData(Collection $rows): CsvClientData
    {
        return $this->clientExtractor->extract(
            $rows->first()->client
        );
    }
}
