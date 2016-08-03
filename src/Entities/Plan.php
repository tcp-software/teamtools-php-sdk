<?php

namespace teamtools\Entities;

use teamtools\Managers\PlanManager;

class Plan extends Entity
{
    protected static $manager = 'teamtools\Managers\PlanManager';

    public static $relationMap = [
        'subscription' => 'teamtools\Entities\Subscription',
        'package'      => 'teamtools\Entities\Package'
    ];
}
