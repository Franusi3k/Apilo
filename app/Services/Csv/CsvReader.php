<?php

namespace app\Services\Csv;

use App\Services\Csv\CsvDelimiterDetector;
use League\Csv\Reader;

class CsvReader
{
    public function __construct(private CsvDelimiterDetector $detector) {}

    public function read($file)
    {
        $content = file_get_contents($file->getRealPath());
        $sample = substr($content, 0, 2048);

        $delimiter = $this->detector->detect($sample);

        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        return collect(iterator_to_array($csv->getRecords()));
    }
};
