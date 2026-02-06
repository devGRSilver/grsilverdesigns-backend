<?php

namespace App\Enums;

enum CouponType: string
{
    case PERCENTAGE   = 'percentage';
    case FIXED_AMOUNT  = 'fixed_amount';
}
