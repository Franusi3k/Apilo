<?php

namespace App\Services\Csv;

use App\DTO\CsvClientData;
use League\ISO3166\ISO3166;

class CsvClientExtractor
{
    public function extract(array $orderRow): CsvClientData
    {
        return new CsvClientData(
            name: trim("{$orderRow['client_firstname']} {$orderRow['client_lastname']}"),
            company: $orderRow['client_company'],
            street: $orderRow['client_street'],
            streetNumber: $orderRow['client_housenr'],
            postcode: $orderRow['client_zip'],
            city: $orderRow['client_city'],
            country: $this->normalizeCountry($orderRow['client_country']),
            phone: $orderRow['client_phone'],
        );
    }

    private function normalizeCountry(string $country): string
    {
        try {
            return (new ISO3166)->name($country)['alpha2'] ?? 'PL';
        } catch (\Throwable $e) {
            return 'PL';
        }
    }
}
