<?php

namespace teamtools\Managers;

use GuzzleHttp\Exception\ClientException;
use teamtools\Entities\Attribute;
use teamtools\TeamToolsClient;

class Manager
{
    protected static $context;
    protected static $entityMap;

    public static function getContext()
    {
        return static::$context;
    }

    public static function getByID($id, TeamToolsClient $client, $raw = false)
    {
        try {
            $response = $client->doRequest('get', [], static::$context . '/' . $id);
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);
        $data           = get_object_vars($responseObject->data);

        return new static::$entityMap($data);
    }

    public static function getByTag($tag, $client, $raw = false)
    {
        $result   = [];

        try {
            $response = $client->doRequest('get', [], static::$context . '/tag/' . $tag);
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
            $result[] = new static::$entityMap($data);
        }

        return new \ArrayIterator($result);
    }

    public static function getAll($args)
    {
        $result = [];
        $query  = [];

        isset($args['keyword'])         && $query['keyword']        = $args['keyword'];
        isset($args['filter'])          && $query['filter']         = $args['filter'];
        isset($args['limit'])           && $query['limit']          = $args['limit'];
        isset($args['offset'])          && $query['offset']         = $args['offset'];
        isset($args['orderBy'])         && $query['orderBy']        = $args['orderBy'];
        isset($args['orderDirection'])  && $query['orderDirection'] = $args['orderDirection'];

        try {
            $response = $args['client']->doRequest('get', $query, static::$context);
        } catch (ClientException $ce) {
            $response = $args['raw'] ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

        if ($args['raw']) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $entity) {
            $data     = get_object_vars($entity);
            $result[] = new static::$entityMap($data);
        }

        return new \ArrayIterator($result);
    }

    public static function getAttributes($client, $raw = false)
    {
        $result = [];

        try {
            $response = $client->doRequest('get', [], static::$context.'/attributes');
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $attribute) {
            $data     = get_object_vars($attribute);
            $result[] = new Attribute($data);
        }

        return new \ArrayIterator($result);
    }

    public static function saveAttribute(Attribute $attribute, $client, $raw = false)
    {
        try {
            if ($attribute->id) {
                $response = $client->doRequest('put', $attribute->prepareForUpdate(), static::$context . '/attributes/' . $attribute->id);
            } else {
                $response = $client->doRequest('post', get_object_vars($attribute), static::$context . '/attributes/');
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

        return new Attribute($data);
    }

    public static function deleteAttribute($id, $client, $raw = false)
    {
        $response = $client->doRequest('delete', [], static::$context . '/attributes/' . $id);

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);
        $data           = get_object_vars($responseObject->data);

        return new Attribute($data);
    }
}