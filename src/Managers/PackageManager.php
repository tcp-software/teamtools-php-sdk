<?php

namespace teamtools\Managers;

use teamtools\Entities\Package;

class PackageManager extends Manager
{
    protected static $context   = 'packages';
    protected static $entityMap = Package::class;
}
