<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Exception;

class PreviewController extends Controller
{
    public function preview(Request $request)
    {
        $file = $request->file('excel_file');

        if (!$file) {
            return response()->json(['error' => 'Brak pliku'], 400);
        }

        try {
            $content = file_get_contents($file->getRealPath());
            $sample = substr($content, 0, 1024);

            $delimiter = $this->detectDelimiter($sample);

            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setDelimiter($delimiter);
            $csv->setHeaderOffset(0);

            $allRecords = iterator_to_array($csv->getRecords());

            $records = collect($allRecords)
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

            return response()->json($records);

        } catch (Exception $e) {
            return response()->json(['error' => 'Błąd przetwarzania pliku: ' . $e->getMessage()], 500);
        }
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
