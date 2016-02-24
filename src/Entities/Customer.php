<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\Managers\CustomerManager;

class Customer extends Entity
{
    protected static $manager = CustomerManager::class;

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

        try {
            $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/endusers');
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

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

        try {
            $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/events');
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

    public function subscribe(array $data, $raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('put', $data, $manager::getContext() . '/' . $this->id . '/subscribe');
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

    public function unsubscribe($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('put', [], $manager::getContext().'/'.$this->id.'/unsubscribe');
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
