<?php

namespace teamtools\Entities;

use teamtools\Managers\FeatureManager;

class Feature extends Entity
{
    protected static $manager = FeatureManager::class;

    public static $relationMap = [
        'packages' 	   => Package::class,
        'dependencies' => Feature::class,
        'dependants'   => Feature::class
    ];
}
