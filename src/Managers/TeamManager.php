<?php

namespace teamtools\Managers;

use teamtools\Entities\Team;

class TeamManager extends Manager
{
    protected static $context   = 'teams';
    protected static $entityMap = Team::class;
}
