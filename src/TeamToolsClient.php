<?php

namespace teamtools;

use teamtools\Entities\Entity;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class TeamToolsClient
{

    const STRIPE_HANDLER_ENDPOINT = 'stripe';
    const TEST_AUTH_DOMAIN        = 'https://develop-auth.dev.teamtools.io/';
    const TEST_API_DOMAIN         = 'https://develop-api.dev.teamtools.io/';

    // protected $authDomain   = 'https://auth.teamtools.io/';
    // protected $apiDomain    = 'https://api.teamtools.io/';
    protected $authDomain   = 'http://auth.teamtools.local/';
    protected $apiDomain    = 'http://api.teamtools.local/';
    protected $guzzleClient;
    protected $accessObject;
    protected static $instance;
    protected $salt;

    public static function initialize(array $config)
    {
        if (null === static::$instance) {
            static::$instance = new static($config);
        }

        Entity::registerClient(static::$instance);
    }

    public static function getInstance()
    {
        if (null === static::$instance) {
            throw new \Exception("Client not initialized.");
        }

        return static::$instance;
    }

    public static function handleStripe()
    {
        $client = static::getInstance();

        $input = file_get_contents('php://input');
        $event = json_decode($input, true);

        return $client->doRequest('post', $event, TeamToolsClient::STRIPE_HANDLER_ENDPOINT);
    }

    protected function __construct(array $config)
    {
        if (isset($config['http_client'])) {
            $httpClient = $config['http_client'];

            if (is_array($httpClient)) {
                $httpClient = new \GuzzleHttp\Client($httpClient);
            } elseif (! $httpClient instanceof \GuzzleHttp\Client) {
                throw new \InvalidArgumentException();
            }
        } else {
            $httpClient = new \GuzzleHttp\Client();
        }

        $this->guzzleClient = $httpClient;
        $this->salt = $config['salt'];

        $authData = [
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'grant_type'    => 'client_credentials'
        ];

        if (isset($config['test']) && $config['test']) {
            $this->authDomain   = self::TEST_AUTH_DOMAIN;
            $this->apiDomain    = self::TEST_API_DOMAIN;
        }

        $response = $this->doRequest('post', $authData, 'access_token', 'auth');

        $this->accessObject = json_decode($response);
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getAccessToken()
    {
        return $this->accessObject->access_token;
    }

    public function doRequest($method, $data, $uri, $resource = 'api', $async = false)
    {
        $requestDataType = $method == 'get' ? 'query' : 'json';

        if ($resource == 'api') {
            $domain = $this->apiDomain;
            $data['access_token'] = $this->accessObject->access_token;
        } elseif ($resource == 'auth') {
            $domain = $this->authDomain;
        }

        if ($async) {
            $method .= 'Async';
            $promise = $this->guzzleClient->$method($domain . $uri, [$requestDataType => $data]);

            $promise->wait();
            
            return true;
        } else {
            $response = $this->guzzleClient->$method($domain . $uri, [$requestDataType => $data]);

            return $response->getBody();
        }

    }
}