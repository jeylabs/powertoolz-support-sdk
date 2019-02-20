<?php
namespace Jeylabs\PowertoolzSupportSdk;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;
class PowertoolzSupportSdk
{
    const VERSION = '1.0.0';
    const GET_LIST_API = 'api/campaign-subscriber';
    const GET_GROUP_API = 'api/campaign-subscriber/group/{campaignSubscriber}';
    const GET_GROUP_LIST_API = 'api/campaign-subscriber/group/{campaignSubscriber}/lists';
    const POST_GROUP_LIST_API = 'api/campaign-subscriber/group/';
    const GET_GROUP_FROM_NAME_API = 'api/campaign-subscriber/group/name';
    const GET_GROUP_FROM_ID_API = 'api/campaign-subscriber/group/from-id';

    const DEFAULT_TIMEOUT = 5;
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
    protected function getDefaultFormParameter() {

        return array_merge([
            'Authorization' => 'Bearer ' . $this->access_token,
        ], $this->formParameters);
    }
    public function getLists($query = [])
    {
        $this->setHeaders(['Content-type' => 'application/json']);
        return $this->makeRequest('GET', self::GET_LIST_API, $query);
    }
    public function getListsFromGroup($query = []) {
        $this->setHeaders(['Content-type' => 'application/json']);
        return $this->makeRequest('GET', self::GET_GROUP_LIST_API, $query);
    }
    public function getGroup($query = []) {
        $this->setHeaders(['Content-type' => 'application/json']);
        return $this->makeRequest('GET', self::GET_GROUP_API, $query);
    }
    public function subscribeListToGroup($query = [], $formParameters = [], $group = null) {
        $this->setHeaders(['Content-type' => 'application/json']);
        $uri = self::POST_GROUP_LIST_API . $group . '/subscribe';
        return $this->makeRequest('POST', $uri, $query, $formParameters);
    }
    public function getGroupFromName($query = [], $formParameters = []) {
        $this->setHeaders(['Content-type' => 'application/json']);
        return $this->makeRequest('GET', self::GET_GROUP_FROM_NAME_API, $query, $formParameters);
    }

    public function getGroupFromId($id) {
        $this->setHeaders(['Content-type' => 'application/json']);
        return $this->makeRequest('POST', self::GET_GROUP_FROM_ID_API, ['id' => $id], []);
    }

    public function __destruct()
    {
        Promise\unwrap($this->promises);
    }
}