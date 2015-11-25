<?php

namespace teamtools\Entities;
use GuzzleHttp\Exception\ClientException;
use teamtools\Managers\CustomerManager;

class Customer extends Entity
{
    protected static $manager = CustomerManager::class;

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
}