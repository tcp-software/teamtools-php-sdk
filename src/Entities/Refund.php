<?php

namespace teamtools\Entities;

use teamtools\Managers\RefundManager;

class Refund extends Entity
{
    protected static $manager = RefundManager::class;
}
