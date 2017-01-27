<?php

namespace teamtools\Entities;

use teamtools\Managers\GroupManager;

class Group extends Entity
{
    protected static $manager = GroupManager::class;
    public static $relationMap = [
        'packages' => Package::class
    ];

    public function getPackages($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/packages');

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
