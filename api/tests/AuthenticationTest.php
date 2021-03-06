<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

/**
 * @internal
 * @coversNothing
 */
class AuthenticationTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function testLogin(): void
    {
        $client = self::createClient();

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword(
            self::$container->get('security.password_encoder')->encodePassword($user, '$3CR3T')
        );

        $manager = self::$container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test IS_AUTHENTICATED_ANONYMOUSLY (not autorized) route
        $client->request('GET', '/news');
        $this->assertResponseIsSuccessful();

        // test IS_AUTHENTICATED_ANONYMOUSLY (authorized) route
        $client->request('GET', '/news', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();

        // test not authorized
        $client->request('POST', '/news', [
            'json' => [
                'title' => 'The Handmaid\'s Tale',
                'text' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('POST', '/news', [
            'json' => [
                'title' => 'The Handmaid\'s Tale',
                'text' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            ],
            'auth_bearer' => $json['token'],
        ]);
        $this->assertResponseIsSuccessful();
    }
}
