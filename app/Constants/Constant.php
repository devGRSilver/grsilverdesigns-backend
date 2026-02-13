<?php

namespace App\Constants;

class Constant
{
    const APP_NAME = 'GR SILVER DESIGN';
    const ACTIVE = 1;
    const IN_ACTIVE = 0;

    const ROLE_USER = 'user';


    const OUT_OF_STOCK = 'out_of_stock';
    const IN_STOCK = 'in_stock';

    const DEFAULT_CURRENCY = "USD";





    // OTP AUTH SYSTEM 
    const OTP_PREFIX = 'otp:';
    const BLOCKED_PREFIX = 'blocked:phone:';
    const MAX_OTP_ATTEMPTS = 3;
    const OTP_TTL = 300; // 5 minutes
    const BLOCK_DURATION = 3600; // 1 hour
    const OTP_LENGTH = 6;
}
