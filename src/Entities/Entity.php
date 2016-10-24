<?php

    namespace teamtools\Entities;

    use GuzzleHttp\Exception\ClientException;
    use teamtools\TeamToolsClient;

    class Entity
    {
        protected static $manager;
        public static $client;
        protected $attributes = [];
        protected $changed = [];

        public function __construct($data)
        {
            $this->attributes = $data;
        }

        public function __set($name, $value)
        {
            $this->changed[$name] = $value;
        }

        public function __get($name)
        {
            if (isset($this->changed[$name])) {
                return $this->changed[$name];
            } elseif (isset($this->attributes[$name])) {
                return $this->attributes[$name];
            } else {
                return null;
            }
        }

        public function __unset($name)
        {
            unset($this->attributes[$name]);
        }

        public function save($raw = false)
        {
            $manager    = static::$manager;

            if (isset($this->attributes['id'])) {
                $response = static::$client->doRequest('put', $this->changed, $manager::getContext() . '/' . $this->attributes['id']);
            } else {
                $response = static::$client->doRequest('post', $this->attributes, $manager::getContext() . '/');
            }

            if ($raw) {
                return (string) $response;
            }

            $responseObject = json_decode($response);
            $data           = get_object_vars($responseObject->data);

            return new static($data);
        }

        public static function saveAll(array $data, $raw = false)
        {
            $manager  = static::$manager;

            $response = static::$client->doRequest('post', ['data' => $data], $manager::getContext() . '/bulk');

            if ($raw) {
                return (string) $response;
            }

            return json_decode($response);

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
                    $include = isset($arguments[1]) ? $arguments[1] : null;
                    $result = call_user_func([static::$manager, 'getByID'], $arguments[0], static::$client, false, $include);
                    break;

                case 'getByIDRaw':
                    $include = isset($arguments[1]) ? $arguments[1] : null;
                    $result = call_user_func([static::$manager, 'getByID'], $arguments[0], static::$client, true, $include);
                    break;

                case 'getByTag':
                    $include = isset($arguments[1]) ? $arguments[1] : null;
                    $result = call_user_func([static::$manager, 'getByTag'], $arguments[0], static::$client, false, $include);
                    break;

                case 'getByTagRaw':
                    $include = isset($arguments[1]) ? $arguments[1] : null;
                    $result = call_user_func([static::$manager, 'getByTag'], $arguments[0], static::$client, true, $include);
                    break;

                case 'getAll':
                    $arguments['client']  = static::$client;
                    $arguments['raw']     = false;
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