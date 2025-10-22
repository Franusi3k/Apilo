<?php

namespace App\Http\Controllers;

use App\Services\Apilo\ApiloService;

class ProductController extends Controller
{
    protected ApiloService $apilo;

    public function __construct(ApiloService $apilo)
    {
        $this->apilo = $apilo;
    }

    public function getProductBySku($sku)
    {
        [$product, $error] = $this->apilo->fetchProductBySku($sku);

        if ($error) {
            return response()->json(['error' => $error], 404);
        }

        return response()->json(['product' => $product], 200);
    }
}
