<?php

namespace teamtools\Managers;

use teamtools\Entities\Feature;

class FeatureManager extends Manager
{
    protected static $context   = 'features';
    protected static $entityMap = Feature::class;
}