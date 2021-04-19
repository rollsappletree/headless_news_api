<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

abstract class AbstractTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    private string $token = '';
    private $clientWithCredentials;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['authorization' => 'Bearer ' . $token]]);
    }

    /**
     * Use other credentials if needed.
     *
     * @param mixed $body
     */
    protected function getToken($body = []): string
    {
        if ($this->token !== '') {
            return $this->token;
        }

        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'johndoe@example.com',
                'password' => 'a password',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = \json_decode($response->getContent());
        $this->token = $data->token;

        return $data->token;
    }
}
