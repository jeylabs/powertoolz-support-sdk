<?php

namespace Jeylabs\PowertoolzSupportSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;

class PowertoolzSupportSdk
{
    const VERSION = '1.0.0';
    const SUBSCRIBE_USER = 'api/subscribe-user';

    const DEFAULT_TIMEOUT = 15;
    protected $client;
    protected $access_token;
    protected $isAsyncRequest = false;
    protected $formParameters = [];
    protected $headers = [];
    protected $promises = [];
    protected $lastResponse;
    protected $postmanApiBabeUri;

    public function __construct($access_token, $postmanApiBabeUri, $isAsyncRequest = false, $httpClient = null)
    {
        $this->access_token = $access_token;
        $this->postmanApiBabeUri = $postmanApiBabeUri;
        $this->isAsyncRequest = $isAsyncRequest;
        $this->headers = ['verify' => false];
        $this->client = $httpClient ?: new Client([
            'base_uri' => $this->postmanApiBabeUri,
            'timeout' => self::DEFAULT_TIMEOUT,
            'connect_timeout' => self::DEFAULT_TIMEOUT,
        ]);
    }

    public function isAsyncRequests()
    {
        return $this->isAsyncRequest;
    }

    public function setAsyncRequests($isAsyncRequest)
    {
        $this->isAsyncRequest = $isAsyncRequest;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders($headers = [])
    {
        $this->headers = $headers;
        return $this;
    }

    public function getFormParameter()
    {
        return $this->formParameters;
    }

    public function setFormParameter($formParameters = [])
    {
        $this->formParameters = $formParameters;
        return $this;
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    protected function makeRequest($method, $uri, $query = [], $formParameters = [])
    {
        $options[GuzzleRequestOptions::FORM_PARAMS] = $formParameters;
        $options[GuzzleRequestOptions::QUERY] = $query;
        $options[GuzzleRequestOptions::HEADERS] = $this->getDefaultHeaders();
        $options['verify'] = false;

        if ($this->isAsyncRequest) {
            return $this->promises[] = $this->client->requestAsync($method, $uri, $options);
        }

        $this->lastResponse = $this->client->request($method, $uri, $options);

        return json_decode($this->lastResponse->getBody(), true);
    }

    protected function getDefaultHeaders()
    {
        return array_merge([
            'Authorization' => 'Bearer ' . $this->access_token,
        ], $this->headers);
    }

    protected function getDefaultFormParameter()
    {

        return array_merge([
            'Authorization' => 'Bearer ' . $this->access_token,
        ], $this->formParameters);
    }

    public function subscribePteSupport($data = [])
    {
        $this->setHeaders(['Content-type' => 'application/json']);
        return $this->makeRequest('POST', self::SUBSCRIBE_USER, $data, []);
    }

    public function __destruct()
    {
        Promise\unwrap($this->promises);
    }
}