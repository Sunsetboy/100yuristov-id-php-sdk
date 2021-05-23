<?php
declare(strict_types=1);

namespace IdService;

use GuzzleHttp\Client;
use IdService\Dto\AuthResult;
use IdService\Dto\UserData;
use IdService\Exceptions\InvalidTokenException;
use IdService\Exceptions\LoginFailedException;

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

    /**
     * Sends users data (emails and password hashes) and returns uids for imported users
     * @param array $usersData [ ['email' => 'email1', 'hash' => 'hash1'] ]
     * @return array [ ['email' => 'email1', 'uid' => 'uid1'] ]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function import(array $usersData): array
    {
        $response = $this->client->request('POST', '/users/import', [
            'json' => $usersData,
            'timeout' => 10,
            'headers' => [
                'X-Auth-Token' => $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $email
     * @param string $password
     * @return AuthResult
     * @throws LoginFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(string $email, string $password): AuthResult
    {
        $response = $this->client->request('POST', '/login', [
            'form_params' => [
                'email' => $email,
                'password' => $password,
            ]
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200) {
            $errorMessage = $responseBody['message'];
            throw new LoginFailedException($errorMessage);
        }

        return new AuthResult($responseBody['uid'], $responseBody['token']);
    }

    /**
     * @param string $token
     * @return string UID
     * @throws InvalidTokenException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkToken(string $token): string
    {
        $response = $this->client->request('GET', '/token/check', [
            'query' => ['token' => $token]
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200) {
            $errorMessage = $responseBody['message'];
            throw new InvalidTokenException($errorMessage);
        }

        return $responseBody['uid'];
    }

    public function updateUser(string $uid, UserData $userData): bool
    {
        $response = $this->client->request('PUT', '/users/' . $uid, [
            'form_params' => [
                'email' => $userData->getEmail(),
                'password' => $userData->getPassword(),
            ],
            'headers' => [
                'X-Auth-Token' => $this->apiKey,
            ],
        ]);

        return $response->getStatusCode() === 200;
    }

    public function addUser(UserData $userData): array
    {
        $response = $this->client->request('POST', '/users', [
            'form_params' => [
                'email' => $userData->getEmail(),
                'password' => $userData->getPassword(),
            ],
            'headers' => [
                'X-Auth-Token' => $this->apiKey,
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 201) {
            $errorMessage = $responseBody['message'];
            throw new UserCreationException($errorMessage);
        }

        return [
            'uid' => $responseBody['uid'],
            'hash' => $responseBody['hash'],
        ];
    }
}