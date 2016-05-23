<?php

namespace teamtools\Entities;

use teamtools\Managers\PaymentManager;

class Payment extends Entity
{
    protected static $manager = PaymentManager::class;
}
