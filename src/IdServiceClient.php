<?php
declare(strict_types=1);

namespace IdService;

use GuzzleHttp\Client;

class IdServiceClient
{
    private $client;

    /**
     * @var string Key for data changing requests
     */
    private $apiKey = '';

    public function __construct(string $baseUrl, string $apiKey = '')
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $baseUrl,
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $this->apiKey = $apiKey;
    }


}