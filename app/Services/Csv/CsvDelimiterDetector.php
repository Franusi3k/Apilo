<?php

namespace App\Services\Csv;

class CsvDelimiterDetector
{
    private array $delimiters = [',', ';', "\t", '|'];

    public function detect(string $sample): string
    {
        $counts = [];

        foreach ($this->delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($sample, $delimiter);
        }

        arsort($counts);

        return key($counts);
    }
}
