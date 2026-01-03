<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOrderRequest;
use App\Services\Apilo\Order\OrderService;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function send(SendOrderRequest $request): JsonResponse
    {
        $response = $this->orderService->sendOrder(
            $request->generalData,
            $request->file('file'),
            $request->notes,
            $request
        );

        return response()->json($response, $response->httpStatus ?? 200);
    }
}
