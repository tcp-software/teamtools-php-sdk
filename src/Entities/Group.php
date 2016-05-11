<?php

namespace teamtools\Entities;

use teamtools\Managers\GroupManager;

class Group extends Entity
{
    protected static $manager = GroupManager::class;

    public function getPackages($raw = false)
    {
        $result  = [];
        $manager = static::$manager;

        try {
            $response = static::$client->doRequest('get', [], $manager::getContext().'/'.$this->id.'/packages');
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
