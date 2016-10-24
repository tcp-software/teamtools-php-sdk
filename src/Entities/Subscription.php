<?php

namespace teamtools\Entities;

use teamtools\Managers\SubscriptionManager;

class Subscription extends Entity
{
    protected static $manager = 'teamtools\Managers\SubscriptionManager';
    public static $relationMap = [
        'package' => 'teamtools\Entities\Package',
        'plan'    => 'teamtools\Entities\Plan',
        'coupon'  => 'teamtools\Entities\Coupon',
    ];


    public function addInvoiceItem(array $data, $raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('put', $data, $manager::getContext().'/'.$this->id.'/addinvoiceitem');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $item) {
            $result[] = $item;
        }

        return new \ArrayIterator($result);
    }
}

