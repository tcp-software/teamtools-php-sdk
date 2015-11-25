<?php

namespace teamtools\Entities;

use teamtools\Managers\PackageManager;

class Package extends Entity
{
    protected static $manager = PackageManager::class;
}