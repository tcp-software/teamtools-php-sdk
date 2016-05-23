<?php

namespace teamtools\Entities;

use teamtools\Managers\SubscriptionManager;

class Subscription extends Entity
{
    protected static $manager = SubscriptionManager::class;
    public static $relationMap = [
        'package' => Package::class,
        'plan'    => Plan::class,
        'coupon'  => Coupon::class,
    ];


    public function addInvoiceItem(array $data, $raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('put', $data, $manager::getContext().'/'.$this->id.'/addinvoiceitem');
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

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

