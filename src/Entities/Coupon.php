<?php

namespace teamtools\Entities;

use teamtools\Managers\CouponManager;

class Coupon extends Entity
{
    protected static $manager = CouponManager::class;
}
