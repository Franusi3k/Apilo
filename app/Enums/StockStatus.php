<?php

namespace App\Enums;

enum StockStatus: string
{
    case CONFIRMED = 'confirmed';
    case PENDING   = 'pending';
    case NOT_FOUND = 'not_found';
}