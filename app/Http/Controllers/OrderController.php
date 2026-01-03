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
        $validated = $request->validated();

        $response = $this->orderService->sendOrder(
            $validated['generalData'],
            $request->file('file'),
            $validated['notes'] ?? null,
            $request
        );

        return response()->json($response, $response->httpStatus ?? 200);
    }
}
