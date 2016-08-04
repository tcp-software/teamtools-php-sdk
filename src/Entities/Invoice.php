<?php

namespace teamtools\Entities;

use teamtools\Managers\InvoiceManager;
use GuzzleHttp\Exception\ClientException;

class Invoice extends Entity
{
    protected static $manager = 'teamtools\Managers\InvoiceManager';
    public static $relationMap = [
        'payments' => 'teamtools\Entities\Payment',
        'refunds'  => 'teamtools\Entities\Refund'
    ];

    public function settle($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('put', [], $manager::getContext().'/'.$this->id.'/settle');
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

    public function applyPayment(array $data, $raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('put', $data, $manager::getContext().'/'.$this->id.'/payment');
        } catch (ClientException $ce) {
            die((string) $ce->getResponse()->getBody());
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

