<?php

namespace teamtools\Managers;

use teamtools\Entities\Subscription;

class SubscriptionManager extends Manager
{
    protected static $context   = 'subscriptions';
    protected static $entityMap = Subscription::class;
}
