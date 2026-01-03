<?php

namespace App\Services\Apilo\Order;

use App\DTO\CsvClientData;

class OrderPayloadFactory
{
    public function make(
        array $generalData,
        CsvClientData $clientData,
        array $items,
        float $totalNet,
        float $totalGross,
        int $carrierAccountId,
        string $notes,
        string $orderedAt
    ): array {
        return [
            'platformId' => config('apilo.platform_id'),
            'idExternal' => 'WWW/' . now()->format('YmdHis'),
            'isInvoice' => true,
            'customerLogin' => $generalData['client'] ?? 'unknown',
            'paymentStatus' => 2,
            'paymentType' => 1,
            'originalCurrency' => 'PLN',
            'originalAmountTotalWithoutTax' => round($totalNet, 2),
            'originalAmountTotalWithTax' => round($totalGross, 2),
            'originalAmountTotalPaid' => round($totalGross, 2),
            'preferences' => [],
            'orderItems' => $items,
            'addressCustomer' => [
                'name' => $generalData['client'] ?? '',
                'phone' => $clientData->phone ?? '',
                'email' => 'shipping@zentrada.com',
                'streetName' => $clientData->street,
                'streetNumber' => $clientData->streetNumber,
                'city' => $clientData->city ?? '',
                'zipCode' => $clientData->postcode ?? '',
                'country' => $clientData->country ?? '',
                'companyTaxNumber' => $generalData['taxNumber'] ?? '',
                'companyName' => $clientData->company ?? '',
            ],
            'addressDelivery' => [
                'name' => $clientData->name ?? '',
                'phone' => $clientData->phone ?? '',
                'email' => 'shipping@zentrada.com',
                'streetName' => $clientData->street,
                'streetNumber' => $clientData->streetNumber,
                'city' => $clientData->city ?? '',
                'zipCode' => $clientData->postcode ?? '',
                'country' => $clientData->country ?? '',
                'companyName' => $clientData->company ?? '',
            ],
            'addressInvoice' => [
                'name' => $clientData->name ?? '',
                'phone' => $clientData->phone ?? '',
                'email' => 'shipping@zentrada.com',
                'streetName' => $clientData->street,
                'streetNumber' => $clientData->streetNumber,
                'city' => $clientData->city ?? '',
                'zipCode' => $clientData->postcode ?? '',
                'country' => $clientData->country ?? '',
                'companyName' => $clientData->company ?? '',
            ],
            'carrierAccount' => $carrierAccountId,
            'orderNotes' => [
                [
                    'type' => '1',
                    'comment' => $notes,
                ],
            ],
            'orderedAt' => $orderedAt,
            'status' => 42,
        ];
    }
}
