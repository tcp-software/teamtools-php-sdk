<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\Managers\EndUserManager;

class EndUser extends Entity
{
    protected static $manager = EndUserManager::class;

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

    public function restore($raw = false)
    {
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('put', [], $manager::getContext().'/'.$this->id.'/restore');
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

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

        try {
            $response = static::$client->doRequest('put', ['ids' => $ids], $manager::getContext().'/restore');
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

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
