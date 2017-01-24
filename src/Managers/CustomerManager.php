<?php

namespace teamtools\Managers;

use teamtools\Entities\Customer;

class CustomerManager extends Manager
{
    protected static $context   = 'customers';
    protected static $entityMap = Customer::class;
}
