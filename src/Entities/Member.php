<?php

namespace teamtools\Entities;

use teamtools\Managers\MemberManager;

class Member extends Entity
{
    protected static $manager = 'teamtools\Managers\MemberManager';

    public static $relationMap = [
        'teams' => 'teamtools\Entities\Team'
    ];
}
