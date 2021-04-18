<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class GreetingsTest extends ApiTestCase
{
    public function testCreateGreeting()
    {
        static::createClient()->request('POST', '/greetings', ['json' => [
            'name' => 'Kévin',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Greeting',
            '@type' => 'Greeting',
            'name' => 'Kévin',
        ]);
    }
}
