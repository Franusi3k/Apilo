<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOrderRequest;
use App\Services\Apilo\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function send(SendOrderRequest $request)
    {
        $response = $this->orderService->sendOrder(
            $request->generalData,
            $request->invoiceData,
            $request->shippingData,
            $request->file('file'),
            $request->notes,
            $request
        );

        return response()->json($response, $response['status_code'] ?? 200);
    }
}
