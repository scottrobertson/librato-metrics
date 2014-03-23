<?php

namespace ScottRobertson\Librato;

class Client
{
  private $api_url = 'https://metrics-api.librato.com';
  private $api_version = 'v1';
  private $email = null;
  private $token = null;

  public function __construct($email, $token)
  {
    $this->email = $email;
    $this->token = $token;
  }

  public function get($path)
  {
    return $this->request($path, 'get');
  }

  public function post($path, array $data = array())
  {
    return $this->request($path, 'post', $data);
  }

  public function gauges(array $gauges = array())
  {
    return $this->post('/metrics', [
      'gauges' => $gauges
    ]);
  }

  private function getAuth()
  {
    return $this->email . ':' . $this->token;
  }

  private function request($path, $method, array $data = array())
  {
    // Setup the Guzzle client with auth, and base url
    $client = new \GuzzleHttp\Client([
        'base_url' => $this->api_url,
        'defaults' => [
          'auth' => [$this->email, $this->token]
        ]
      ]
    );

    // Send any POST data
    if ($method === 'post') {
      
      $response = $client
        ->post('/' . $this->api_version . $path, [
          'body' => json_encode($data),
          'headers' => [
            'Content-Type' => 'application/json'
         ]
        ]);

      return $response->getStatusCode() == 200;
    }
      
    // Default to GET
    return $client
      ->get('/' . $this->api_version . $path)
      ->json();
  }

}
