<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\TeamToolsClient;

class Entity
{
    protected static $manager;
    public static $client;
    public $id;

    public function __construct($data)
    {
        foreach ($data as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    public function save($raw = false)
    {
        $attributes = get_object_vars($this);
        $manager    = static::$manager;

        try {
            if ($this->id) {
                $response = static::$client->doRequest('put', $attributes, $manager::getContext() . '/' . $this->id);
            } else {
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

    public function delete($raw = false)
    {
        if ($this->id) {
            $manager = static::$manager;

            $response = static::$client->doRequest('delete', [], $manager::getContext() . '/' . $this->id);

            if ($raw) {
                return (string) $response;
            }

            $responseObject = json_decode($response);
            $data           = get_object_vars($responseObject->data);

            return new static($data);
        } else {
            throw new \Exception("Can't delete non-existing entity.");
        }
    }

    public static function registerClient(TeamToolsClient $client)
    {
        static::$client = $client;
    }

    public static function __callStatic($name, $arguments)
    {
        switch ($name) {
            case 'getByID':
                $result = call_user_func([static::$manager, 'getByID'], $arguments[0], static::$client);
                break;

            case 'getByIDRaw':
                $result = call_user_func([static::$manager, 'getByID'], $arguments[0], static::$client, true);
                break;

            case 'getByTag':
                $result = call_user_func([static::$manager, 'getByTag'], $arguments[0], static::$client);
                break;

            case 'getByTagRaw':
                $result = call_user_func([static::$manager, 'getByTag'], $arguments[0], static::$client, true);
                break;

            case 'getAll':
                $arguments['client'] = static::$client;
                $arguments['raw']    = false;
                $result = call_user_func([static::$manager, 'getAll'], $arguments);
                break;

            case 'getAllRaw':
                $arguments['client'] = static::$client;
                $arguments['raw'] = true;
                $result = call_user_func([static::$manager, 'getAll'], $arguments);
                break;

            case 'getAttributes':
                $result = call_user_func([static::$manager, 'getAttributes'], static::$client);
                break;

            case 'getAttributesRaw':
                $result = call_user_func([static::$manager, 'getAttributes'], static::$client, true);
                break;

            case 'saveAttribute':
                $raw = isset($arguments[1]) ? $arguments[1] : false;
                $result = call_user_func([static::$manager, 'saveAttribute'], $arguments[0], static::$client, $raw);
                break;

            case 'deleteAttribute':
                $raw = isset($arguments[1]) ? $arguments[1] : false;
                $result = call_user_func([static::$manager, 'deleteAttribute'], $arguments[0], static::$client, $raw);
                break;

            default:
                break;
        }

        return $result;
    }
}