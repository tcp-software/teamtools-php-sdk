<?php

namespace teamtools\Managers;

use teamtools\Entities\WebEvent;

class WebEventManager extends Manager
{
    protected static $context   = 'webevents';
    protected static $entityMap = WebEvent::class;
}
