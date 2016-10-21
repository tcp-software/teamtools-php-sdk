<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\Managers\EndUserManager;

class EndUser extends Entity
{
    protected static $manager = 'teamtools\Managers\EndUserManager';
    public static $relationMap = [
        'customer' => 'teamtools\Entities\Customer',
        'events'   => 'teamtools\Entities\Event'
    ];

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
}
