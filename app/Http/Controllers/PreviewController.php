<?php

namespace App\Http\Controllers;

use App\Services\PreviewService;
use Illuminate\Http\Request;
use League\Csv\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class PreviewController extends Controller
{
    public function __construct(private readonly PreviewService $previewService) {}

    public function preview(Request $request): JsonResponse
    {
        $file = $request->file('excel_file');

        if (! $file) {
            return response()->json(['error' => 'Brak pliku'], 400);
        }

        try {
            $records = $this->previewService->parseCsv($file);

            return response()->json($records);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Błąd przetwarzania pliku: ' . $e->getMessage(),
            ], 500);
        }
    }
}
