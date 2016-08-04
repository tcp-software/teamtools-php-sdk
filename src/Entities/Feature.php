<?php

namespace teamtools\Entities;

use teamtools\Managers\FeatureManager;

class Feature extends Entity
{
    protected static $manager = 'teamtools\Managers\FeatureManager';

    public static $relationMap = [
        'packages' 	   => 'teamtools\Entities\Package',
        'dependencies' => 'teamtools\Entities\Feature',
        'dependants'   => 'teamtools\Entities\Feature'
    ];
}
