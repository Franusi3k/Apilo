<?php

namespace App\Http\Controllers;

use App\Services\PreviewService;
use Illuminate\Http\Request;
use League\Csv\Exception;

class PreviewController extends Controller
{
    protected PreviewService $previewService;

    public function __construct(PreviewService $previewService)
    {
        $this->previewService = $previewService;
    }

    public function preview(Request $request)
    {
        $file = $request->file('excel_file');

        if (!$file) {
            return response()->json(['error' => 'Brak pliku'], 400);
        }

        try {
            $records = $this->previewService->parseCsv($file);
            return response()->json($records);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Błąd przetwarzania pliku: ' . $e->getMessage(),
            ], 500);
        }
    }
}
