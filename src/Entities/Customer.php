<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\Managers\CustomerManager;

class Customer extends Entity
{
    protected static $manager = 'teamtools\Managers\CustomerManager';
    public static $relationMap = [
        'subscription' => 'teamtools\Entities\Subscription',
        'invoices'     => 'teamtools\Entities\Invoice',
        'users'        => 'teamtools\Entities\EndUser',
    ];

    public function save($raw = false)
    {
        // Email has unique validator.
        // If value passed is same as what's already in database, validator will fail.
        // Unset email param if same as in db
        if (isset($this->id) && isset($this->email)) {
            $teamValidation = Customer::getByID($this->id);

            if ($teamValidation->email == $this->email) {
                unset($this->email);
            }
        }

        return parent::save($raw);
    }

    public function getEndUsers($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/endusers');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $endUser) {
            $data     = get_object_vars($endUser);
            $result[] = new EndUser($data);
        }

        return new \ArrayIterator($result);
    }

    public function getEvents($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/events');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $item) {
            $result[] = $item;
        }

        return new \ArrayIterator($result);
    }

    public function subscribe(array $data, $raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('put', $data, $manager::getContext() . '/' . $this->id . '/subscribe');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);
        $data           = get_object_vars($responseObject->data);

        return new Subscription($data);
    }

    public function unsubscribe($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('put', [], $manager::getContext().'/'.$this->id.'/unsubscribe');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $item) {
            $result[] = $item;
        }

        return new \ArrayIterator($result);
    }

    public function getSubscription($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/subscription');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $item) {
            $result[] = $item;
        }

        return new \ArrayIterator($result);
    }

    public function restore($raw = false)
    {
        $manager = static::$manager;

        $response = static::$client->doRequest('put', [], $manager::getContext().'/'.$this->id.'/restore');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);
        $data           = get_object_vars($responseObject->data);

        return new static($data);
    }

    public static function restoreAll(array $ids, $raw = false)
    {
        $manager = static::$manager;
        $result   = [];

        $response = static::$client->doRequest('put', ['ids' => $ids], $manager::getContext().'/restore');

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $entity) {
            $data     = get_object_vars($entity);
            $result[] = new static($data);
        }

        return new \ArrayIterator($result);
    }

    public function migrateEndusers($newCustomerId, array $ids = [], $raw = false)
    {
        $manager = static::$manager;
        $result   = [];

        $response = static::$client->doRequest(
            'put', 
            ['enduserIds' => $ids, 'newCustomerId' => $newCustomerId], 
            $manager::getContext() . '/' . $this->id . '/endusers'
        );

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $entity) {
            $data     = get_object_vars($entity);
            $result[] = new static($data);
        }

        return new \ArrayIterator($result);
    }
}
