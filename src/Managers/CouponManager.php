<?php

namespace teamtools\Managers;

use teamtools\Entities\Coupon;

class CouponManager extends Manager
{
    protected static $context   = 'coupons';
    protected static $entityMap = Coupon::class;
}
