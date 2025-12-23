<?php

namespace App\Services\Apilo\Order;

use App\Services\Apilo\ApiloService;

class OrderStockUpdater
{
    public function __construct(private readonly ApiloService $apiloService) {}

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

            if (! $result->success) {
                $success = false;
            }
        }

        return $success;
    }
}
