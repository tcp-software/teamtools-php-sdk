<?php

namespace teamtools\Entities;

use teamtools\Managers\PackageManager;

class Package extends Entity
{
    protected static $manager = 'teamtools\Managers\PackageManager';
    public static $relationMap = [
        'features' => 'teamtools\Entities\Feature',
        'plan'     => 'teamtools\Entities\Plan',
        'group'    => 'teamtools\Entities\Group',
    ];
}
