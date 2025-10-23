<?php

namespace App\Services;

use League\Csv\Reader;

class PreviewService
{
    public function parseCsv($file)
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
                    'name' => $this->safeConvert($cols[1] ?? '', 'UTF-8', 'auto'),
                    'quantity' => $this->safeConvert($cols[4] ?? '', 'UTF-8', 'auto'),
                    'price' => $this->safeConvert($cols[5] ?? '', 'UTF-8', 'auto'),
                    'sku' => $this->safeConvert($cols[6] ?? '', 'UTF-8', 'auto'),
                    'netto' => $this->safeConvert($cols[8] ?? '', 'UTF-8', 'auto'),
                    'currency' => $this->safeConvert($cols[13] ?? '', 'UTF-8', 'auto'),
                    'ean' => $this->safeConvert($cols[14] ?? '', 'UTF-8', 'auto'),

                    // client data
                    'client_firstname' => $this->safeConvert($cols[15] ?? '', 'UTF-8', 'auto'),
                    'client_lastname' => $this->safeConvert($cols[16] ?? '', 'UTF-8', 'auto'),
                    'client_company' => $this->safeConvert($cols[17] ?? '', 'UTF-8', 'auto'),
                    'client_street' => $this->safeConvert($cols[18] ?? '', 'UTF-8', 'auto'),
                    'client_housenr' => $this->safeConvert($cols[19] ?? '', 'UTF-8', 'auto'),
                    'client_zip' => $this->safeConvert($cols[21] ?? '', 'UTF-8', 'auto'),
                    'client_city' => $this->safeConvert($cols[22] ?? '', 'UTF-8', 'auto'),
                    'client_country' => $this->safeConvert($cols[23] ?? '', 'UTF-8', 'auto'),
                    'client_phone' => $this->safeConvert($cols[24] ?? '', 'UTF-8', 'auto'),
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

    private function safeConvert(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // spróbuj automatycznie
        $encoded = @mb_convert_encoding($value, 'UTF-8', 'auto');

        if ($encoded === false) {
            // fallback dla plików z Windowsa
            $encoded = @mb_convert_encoding($value, 'UTF-8', 'Windows-1250');
        }

        // ostateczny fallback – zwróć oryginał
        return $encoded ?: (string) $value;
    }
}
