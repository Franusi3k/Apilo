<?php

namespace App\Services\Apilo\Order;

use App\DTO\ApiloResult;
use App\Services\Apilo\ApiloService;
use Illuminate\Support\Facades\Log;

class OrderStockUpdater
{
    public function __construct(private ApiloService $apiloService) {}

    public function updateStock(array $items): bool 
    {
        $success = true;

        foreach ($items as $item) {
            $sku = $item['sku'];
            $qty = (int) $item['quantity'];

            if (! $sku || $qty <= 0) {
                continue;
            }

            $result = $this->apiloService->updateStockQuantity($sku, $qty);

            if (!$result->success) $success = false;
        }

        return $success;
    }
}
