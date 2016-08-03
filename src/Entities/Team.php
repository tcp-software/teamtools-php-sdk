<?php

namespace teamtools\Entities;

use teamtools\Managers\TeamManager;

class Team extends Entity
{
    protected static $manager = 'teamtools\Managers\TeamManager';

    public static $relationMap = [
        'members' => 'teamtools\Entities\Member'
    ];
}
