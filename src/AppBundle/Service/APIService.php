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
    ]);
      
    	return $this;
    }

    public function getData()
    {
      return $this->request->getBody()->getContents();
    }
    
    public function setData($data)
    {
      $this->data = $data;

      return $this;
    }
	
    public function setUri($uri)
    {
      $this->uri = $uri;

      return $this;
    }
    
    public function setMethod($method)
    {
      $this->method = $method;

      return $this;
    }
}