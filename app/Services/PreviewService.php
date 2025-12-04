<?php

namespace App\Services;

use Illuminate\Support\Collection;
use League\Csv\Reader;
use League\ISO3166\ISO3166;

class PreviewService
{
    public function parseCsv($file): Collection
    {
        $content = file_get_contents($file->getRealPath());
        $sample = substr($content, 0, 1024);
        $delimiter = $this->detectDelimiter($sample);

        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        $allRecords = iterator_to_array($csv->getRecords());

        return collect($allRecords)
            ->map(function ($row) {
                $cols = array_values($row);

                return [
                    // order data
                    'name' => safeConvert($cols[1] ?? ''),
                    'quantity' => safeConvert($cols[4] ?? ''),
                    'price' => safeConvert($cols[5] ?? ''),
                    'sku' => safeConvert($cols[6] ?? ''),
                    'netto' => safeConvert($cols[8] ?? ''),
                    'currency' => safeConvert($cols[13] ?? ''),
                    'ean' => safeConvert($cols[14] ?? ''),

                    // client data
                    'client_firstname' => safeConvert($cols[15] ?? ''),
                    'client_lastname' => safeConvert($cols[16] ?? ''),
                    'client_company' => safeConvert($cols[17] ?? ''),
                    'client_street' => safeConvert($cols[18] ?? ''),
                    'client_housenr' => safeConvert($cols[19] ?? ''),
                    'client_zip' => safeConvert($cols[21] ?? ''),
                    'client_city' => safeConvert($cols[22] ?? ''),
                    'client_country' => safeConvert($cols[23] ?? ''),
                    'client_phone' => safeConvert($cols[24] ?? ''),
                ];
            })
            ->values();
    }

    private function detectDelimiter(string $sample): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $counts = [];

        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($sample, $delimiter);
        }

        arsort($counts);

        return key($counts);
    }

    public function extractClientData(Collection $records): array
    {
        if ($records->isEmpty()) {
            return [];
        }

        $first = $records->first();
        $iso3166 = new ISO3166;

        return [
            'name' => trim(($first['client_firstname'] ?? '') . ' ' . ($first['client_lastname'] ?? '')),
            'company' => $first['client_company'] ?? '',
            'street' => $first['client_street'] ?? '',
            'streetNumber' => $first['client_housenr'] ?? '',
            'postcode' => $first['client_zip'] ?? '',
            'city' => $first['client_city'] ?? '',
            'country' => $iso3166->name($first['client_country'])['alpha2'] ?? 'PL',
            'phone' => $first['client_phone'] ?? '',
        ];
    }
}
