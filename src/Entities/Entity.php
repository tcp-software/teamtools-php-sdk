<?php

namespace teamtools\Entities;

use GuzzleHttp\Exception\ClientException;
use teamtools\TeamToolsClient;

class Entity
{
    protected static $manager;
    public static $client;
    public $id;
    protected $changedProperties = [];
    private $initializing = false;

    public function __construct($data)
    {
        $this->initializing = true;

        foreach ($data as $attribute => $value) {
            $this->$attribute = $value;
        }

        $this->initializing = false;
    }

    public function __set($name, $value)
    {
        if (!$this->initializing) {
            $this->changedProperties[$name] = $value;
        }
        
        $this->$name = $value;
    }

    /**
     * When entity object is constructed by ID, all attributes are initialized from database.
     * Then we change some editable attribute and attempt to save object.
     * This request will also contain all other attributes that were initialized from database (some of them non-editable).
     * This way we won't be able to update single attribute if there is at least 1 non-editable attribute on entity.
     * This is helper function that takes only editable (and existing) attributes for PUT request.
     * Also, if attribute type is date and it's initialized from database - it needs to be flattened.
     */
    protected function getUpdateableAttributes()
    {
        /*$attributes = $this::getAttributes();
        $properties = get_object_vars($this);
        $result = [];

        foreach ($attributes as $attr) {
            if (isset($attr->editable) && $attr->editable && isset($properties[$attr->name])) {
                $result[$attr->name] = $properties[$attr->name];
            }

            // Convert date object to string
            if (isset($attr->type) && $attr->type == 'date' && isset($properties[$attr->name])) {
                $dateObject = $properties[$attr->name];

                if (isset($dateObject->date)) {
                    $result[$attr->name] = $dateObject->date;
                }
            }
        }

        return $result;*/

        return $this->changedProperties;
    }

    public function save($raw = false)
    {
        $attributes = get_object_vars($this);
        $manager    = static::$manager;

        try {
            if ($this->id) {
                $response = static::$client->doRequest('put', $this->getUpdateableAttributes(), $manager::getContext() . '/' . $this->id);
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
