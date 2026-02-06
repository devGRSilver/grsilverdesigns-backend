<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT   = 'pending_payment';
    case PAYMENT_RECEIVED  = 'payment_received';
    case CONFIRMED         = 'confirmed';
    case PROCESSING        = 'processing';
    case PACKED            = 'packed';
    case SHIPPED           = 'shipped';
    case OUT_FOR_DELIVERY  = 'out_for_delivery';
    case DELIVERED         = 'delivered';
    case CANCEL_REQUESTED  = 'cancel_requested';
    case CANCELLED         = 'cancelled';
        // case RETURN_REQUESTED  = 'return_requested';
        // case RETURN_APPROVED   = 'return_approved';
        // case RETURNED          = 'returned';
        // case REFUNDED          = 'refunded';
    case FAILED            = 'failed';
}
