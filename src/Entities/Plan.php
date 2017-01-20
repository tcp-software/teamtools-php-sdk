<?php

namespace teamtools\Entities;

use teamtools\Managers\PlanManager;

class Plan extends Entity
{
    protected static $manager = PlanManager::class;

    public static $relationMap = [
        'subscription' => Subscription::class,
        'package'      => Package::class
    ];
}
