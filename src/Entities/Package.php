<?php

namespace teamtools\Entities;

use teamtools\Managers\PackageManager;

class Package extends Entity
{
    protected static $manager = PackageManager::class;
    public static $relationMap = [
        'features' => Feature::class,
        'plan'     => Plan::class,
        'group'    => Group::class,
    ];
}
