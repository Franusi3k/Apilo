<?php

namespace App\Services\Csv;

use App\DTO\CsvClientData;
use League\ISO3166\ISO3166;
use Throwable;

class CsvClientExtractor
{
    public function extract(array $client): CsvClientData
    {
        return new CsvClientData(
            name: trim("{$client['firstname']} {$client['lastname']}"),
            company: $client['company'],
            street: $client['street'],
            streetNumber: $client['housenr'],
            postcode: $client['zip'],
            city: $client['city'],
            country: $this->normalizeCountry($client['country']),
            phone: $client['phone'],
        );
    }

    private function normalizeCountry(string $country): string
    {
        try {
            return (new ISO3166)->name($country)['alpha2'] ?? 'PL';
        } catch (Throwable) {
            return 'PL';
        }
    }
}
