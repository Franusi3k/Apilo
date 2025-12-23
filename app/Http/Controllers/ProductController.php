<?php

namespace App\Http\Controllers;

use App\Services\Apilo\ApiloService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ApiloService $apilo) {}

    public function getProductBySku(string $sku): JsonResponse
    {
        $result = $this->apilo->fetchProductBySku($sku);

        if (!$result->success) {
            return response()->json(['message' => $result->message], 404);
        }

        return response()->json(['product' => $result->data]);
    }
}
