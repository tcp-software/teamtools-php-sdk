<?php

namespace teamtools\Managers;

use teamtools\Entities\EndUser;

class EndUserManager extends Manager
{
    protected static $context   = 'endusers';
    protected static $entityMap = EndUser::class;
}
