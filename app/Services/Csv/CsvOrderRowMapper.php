<?php

namespace App\Services\Csv;

use App\DTO\CsvOrderLine;

class CsvOrderRowMapper
{
    private const MAP = [
        'name' => 1,
        'quantity' => 4,
        'price' => 12,
        'sku' => 6,
        'netto' => 11,
        'currency' => 13,
        'ean' => 14,

        'client_firstname' => 15,
        'client_lastname' => 16,
        'client_company' => 17,
        'client_street' => 18,
        'client_housenr' => 19,
        'client_zip' => 21,
        'client_city' => 22,
        'client_country' => 23,
        'client_phone' => 24,
    ];

    public function map(array $row): CsvOrderLine
    {
        $cols = array_values($row);
        $mapped = [];

        foreach (self::MAP as $key => $index) {
            $mapped[$key] = safeConvert($cols[$index] ?? '');
        }

        return CsvOrderLine::fromArray($mapped);
    }
}
