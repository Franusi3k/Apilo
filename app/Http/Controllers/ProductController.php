<?php

namespace App\Http\Controllers;

use App\Services\Apilo\ApiloService;

class ProductController extends Controller
{
    public function __construct(private ApiloService $apilo) {}

    public function getProductBySku(int $sku)
    {
        [$product, $error] = $this->apilo->fetchProductBySku($sku);

        if ($error) {
            return response()->json(['error' => $error], 404);
        }

        return response()->json(['product' => $product], 200);
    }
}
