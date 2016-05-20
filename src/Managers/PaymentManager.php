<?php

namespace teamtools\Managers;

use teamtools\Entities\Payment;

class PaymentManager extends Manager
{
    protected static $context   = 'payments';
    protected static $entityMap = Payment::class;
}
