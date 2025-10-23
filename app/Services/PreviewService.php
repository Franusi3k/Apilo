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
                    'name' => mb_convert_encoding($cols[1] ?? '', 'UTF-8', 'auto'),
                    'quantity' => mb_convert_encoding($cols[4] ?? '', 'UTF-8', 'auto'),
                    'price' => mb_convert_encoding($cols[5] ?? '', 'UTF-8', 'auto'),
                    'sku' => mb_convert_encoding($cols[6] ?? '', 'UTF-8', 'auto'),
                    'netto' => mb_convert_encoding($cols[8] ?? '', 'UTF-8', 'auto'),
                    'currency' => mb_convert_encoding($cols[13] ?? '', 'UTF-8', 'auto'),
                    'ean' => mb_convert_encoding($cols[14] ?? '', 'UTF-8', 'auto'),

                    // client data
                    'client_firstname' => mb_convert_encoding($cols[15] ?? '', 'UTF-8', 'auto'),
                    'client_lastname' => mb_convert_encoding($cols[16] ?? '', 'UTF-8', 'auto'),
                    'client_company' => mb_convert_encoding($cols[17] ?? '', 'UTF-8', 'auto'),
                    'client_street' => mb_convert_encoding($cols[18] ?? '', 'UTF-8', 'auto'),
                    'client_housenr' => mb_convert_encoding($cols[19] ?? '', 'UTF-8', 'auto'),
                    'client_zip' => mb_convert_encoding($cols[21] ?? '', 'UTF-8', 'auto'),
                    'client_city' => mb_convert_encoding($cols[22] ?? '', 'UTF-8', 'auto'),
                    'client_country' => mb_convert_encoding($cols[23] ?? '', 'UTF-8', 'auto'),
                    'client_phone' => mb_convert_encoding($cols[24] ?? '', 'UTF-8', 'auto'),
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
}
