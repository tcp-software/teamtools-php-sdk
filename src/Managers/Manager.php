<?php

namespace teamtools\Managers;

use GuzzleHttp\Exception\ClientException;
use teamtools\Entities\Attribute;
use teamtools\TeamToolsClient;
use teamtools\Entities\Entity;

class Manager
{
    protected static $context;
    protected static $entityMap;
    protected static $relationKeys;

    public static function getContext()
    {
        return static::$context;
    }

    public static function getByID($id, TeamToolsClient $client, $raw = false, $include = null)
    {
        try {
            $response = $client->doRequest('get', [], static::$context . '/' . $id, 'api', $include);
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);
        $data           = get_object_vars($responseObject->data);

        $sdkObject = new static::$entityMap($data);
        
        static::parseIncludes($sdkObject, $include, $data);

        return $sdkObject;
    }

    public static function getByTag($tag, $client, $raw = false, $include = false)
    {
        $result   = [];

        try {
            $response = $client->doRequest('get', [], static::$context . '/tag/' . $tag, 'api', $include);
        } catch (ClientException $ce) {
            $response = $raw ? (string) $ce->getResponse()->getBody() : json_decode($ce->getResponse()->getBody());
            return $response;
        }

        if ($raw) {
            return (string) $response;
        }

        $responseObject = json_decode($response);

        foreach ($responseObject->data as $entity) {
            $data      = get_object_vars($entity);
            $sdkObject = new static::$entityMap($data);
            
            static::parseIncludes($sdkObject, $include, $data);

            $result[] = $sdkObject;
        }

        return new \ArrayIterator($result);
    }

    public static function getAll($args)
    {
        $result = [];
        $query  = [];

        isset($args[0]['keyword'])         && $query['keyword']        = $args[0]['keyword'];
        isset($args[0]['filter'])          && $query['filter']         = $args[0]['filter'];
        isset($args[0]['limit'])           && $query['limit']          = $args[0]['limit'];
        isset($args[0]['offset'])          && $query['offset']         = $args[0]['offset'];
        isset($args[0]['orderBy'])         && $query['orderBy']        = $args[0]['orderBy'];
        isset($args[0]['orderDirection'])  && $query['orderDirection'] = $args[0]['orderDirection'];

        $include = isset($args[0]['include']) ? $args[0]['include'] : null;

        try {
            $response = $args['client']->doRequest('get', $query, static::$context, 'api', $include);
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
            $sdkObject = new static::$entityMap($data);
            
            static::parseIncludes($sdkObject, $include, $data);

            $result[] = $sdkObject;
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

    public static function parseIncludes(Entity $entity, $include, $responseData)
    {
        // Include expressions are separated by comma: invoices,subscription.package.features.
        // Here we attach inclusions for each expression
        if ($entity::$relationMap && $include) {
            $includes = explode(',', $include);

            // Recursive inclusion of entities separated by dot
            foreach ($includes as $includeKey) {
                static::attachRelated($entity, $includeKey, $responseData);
            }
        }
    }

    public static function attachRelated(Entity $entity, $include, $responseData)
    {
        // Explode into 2 parts: entity key for inclusion and rest of the expression
        $includes = explode('.', $include, 2);

        $inclusionEntityKey = $includes[0];
        $restOfExpression   = isset($includes[1]) ? $includes[1] : null;

        $class = $entity::$relationMap[$inclusionEntityKey];
        
        if (!$responseData[$inclusionEntityKey]) {
            return;
        }

        $rawData = $responseData[$inclusionEntityKey]->data;

        if (is_array($rawData)) {
            $relatedObject = [];

            foreach ($rawData as $key => $value) {
                $tmpObject = new $class(get_object_vars($value));
                $relatedObject[] = $tmpObject;


                if ($restOfExpression) {
                    die(var_dump($value));
                    static::attachRelated(
                        $tmpObject, 
                        $restOfExpression, 
                        get_object_vars($value)
                    );
                }
            }
        } else {
            $relatedObject = new $class(get_object_vars($rawData));
        }

        $entity->$inclusionEntityKey = $relatedObject;

        if ($restOfExpression) {
            if (!is_array($relatedObject)) {
                static::attachRelated(
                    $relatedObject, 
                    $restOfExpression, 
                    get_object_vars($responseData[$inclusionEntityKey]->data)
                );
            }
            
        }
    }
}
