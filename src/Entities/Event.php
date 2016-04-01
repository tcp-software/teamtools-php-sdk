<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\Managers\EventManager;

class Event extends Entity
{
    protected static $manager = EventManager::class;

    public $id;
    public $name;
    public $endUser;
    public $endUserId;
    public $endUserName;
    public $timestamp;
    public $metadata;


    public function save($raw = false)
    {
        $attributes        = get_object_vars($this);
        $manager           = static::$manager;
        $attributes['key'] = $this->getSaveKey();

        try {
            if (!$this->id) {
                $response = static::$client->doRequest('post', $attributes, $manager::getContext() . '/');
            }
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

    public function getSaveKey()
    {
        return md5($this->endUserId . static::$client->getSalt());
    }

    public static function __callStatic($name, $arguments)
    {
        switch ($name) {
            case 'getByID':
                $result = call_user_func([static::$manager, 'getByID'], $arguments[0], static::$client);
                break;

            default:
                return;
                break;
        }

        return $result;
    }
}
