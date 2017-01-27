<?php

namespace teamtools\Entities;

use teamtools\Managers\MemberManager;

class Member extends Entity
{
    protected static $manager = MemberManager::class;

    public static $relationMap = [
        'teams' => Team::class
    ];
}
