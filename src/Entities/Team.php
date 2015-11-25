<?php

namespace teamtools\Entities;

use teamtools\Managers\TeamManager;

class Team extends Entity
{
    protected static $manager = TeamManager::class;
}