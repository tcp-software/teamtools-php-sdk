<?php

namespace teamtools\Managers;

use teamtools\Entities\Invoice;

class InvoiceManager extends Manager
{
    protected static $context   = 'invoices';
    protected static $entityMap = Invoice::class;
}
