<?php

namespace teamtools\Entities;

use teamtools\Managers\WebEventManager;

class WebEvent extends Entity
{
    protected static $manager = WebEventManager::class;
    public static $relationMap = [];
}
