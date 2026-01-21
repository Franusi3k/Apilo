<?php

namespace App\Services\Apilo\Order;

use App\Services\Apilo\ApiloService;

// I created that class and whole logic to updating product stock before API added auto stock update on order creation.
// So now it's redundant, but I leave it here just in case we need it in the future.
class OrderStockUpdater
{
    public function __construct(private readonly ApiloService $apiloService) {}

    public function updateStock(array $items): bool
    {
        $success = true;
        $payload = [];

        foreach ($items as $item) {
            $product = $item->product ?? null;
            $csv = $item->csv ?? null;
            $sku = $csv->sku ?? null;
            $qty = (int) ($item->requestedQuantity ?? ($csv->quantity ?? 0));

            if (! $product || ! $sku || $qty <= 0) {
                $success = false;
                continue;
            }

            if (empty($product['id'])) {
                $success = false;
                continue;
            }

            $payload[] = $this->apiloService->createStockPayloadItem($product, $sku, $qty);
        }

        if ($payload === []) {
            return $success;
        }

        $result = $this->apiloService->updateStockQuantities($payload);

        return $result->success && $success;
    }
}
