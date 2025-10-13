<?php

namespace App\Services;

use League\Csv\Reader;
use League\Csv\Exception;

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
                    'name' => isset($cols[1]) ? mb_convert_encoding($cols[1], 'UTF-8', 'auto') : '',
                    'quantity' => isset($cols[4]) ? mb_convert_encoding($cols[4], 'UTF-8', 'auto') : '',
                    'price' => isset($cols[5]) ? mb_convert_encoding($cols[5], 'UTF-8', 'auto') : '',
                    'sku' => isset($cols[6]) ? mb_convert_encoding($cols[6], 'UTF-8', 'auto') : '',
                    'netto' => isset($cols[8]) ? mb_convert_encoding($cols[8], 'UTF-8', 'auto') : '',
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
