<?php

namespace teamtools\Managers;

use teamtools\Entities\Member;

class MemberManager extends Manager
{
    protected static $context   = 'members';
    protected static $entityMap = Member::class;
}