<?php
namespace AppBundle\Service;

use GuzzleHttp\Client;

/**
* APIService
*/
class APIService
{
    private $client;
    private $apiKey;
	  private $uri;
    private $request;
    private $method = 'GET';
    private $data = [];
    private $query = [];

    public function __construct($apiBaseUri, $apiKey)
    {
		  $this->client = new Client([
        'base_uri' => $apiBaseUri,
      ]);
      $this->apiKey = $apiKey;
    }

    public function connect()
    {
      $this->request = $this->client->request($this->method, $this->uri, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' =>  $this->apiKey,
        ],
        'json' => $this->data,
        'query' => $this->query
    ]);
      
    	return $this;
    }

    public function getData()
    {
      return $this->request->getBody()->getContents();
    }
    
    public function setData(array $data)
    {
      $this->data = $data;

      return $this;
    }
	
    public function setUri(string $uri)
    {
      $this->uri = $uri;

      return $this;
    }
    
    public function setQuery(array $query)
    {
      $this->query = $query;

      return $this;
    }

    public function setMethod(string  $method)
    {
      $this->method = $method;

      return $this;
    }
}