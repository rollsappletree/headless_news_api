<?php

declare(strict_types=1);

// api/tests/NewsTest.php

namespace App\Tests\Api;

use App\Entity\News;
use App\Tests\AbstractTest;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @internal
 * @coversNothing
 */
class NewsTest extends AbstractTest
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/news');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/contexts/News',
            '@id' => '/news',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 20,
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(20, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(News::class);
    }

    public function testCreateNews(): void
    {
        $response = $this->createClientWithCredentials()->request('POST', '/news', ['json' => [
            'title' => 'The Handmaid\'s Tale',
            'text' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/News',
            '@type' => 'News',
            'title' => 'The Handmaid\'s Tale',
            'text' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
        ]);
        $this->assertMatchesRegularExpression('~^/news/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(News::class);
    }

    public function testUpdateNews(): void
    {
        $client = $this->createClientWithCredentials();
        $slugger = new AsciiSlugger();
        $slug = $slugger->slug('updated title')->toString();
        $iri = $this->findIriBy(News::class, ['slug' => 'et-sit-enim-omnis']);

        $client->request('PUT', $iri, ['json' => [
            'title' => 'updated title',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'slug' => $slug,
            'title' => 'updated title',
        ]);
    }

    public function testDeleteNews(): void
    {
        $client = $this->createClientWithCredentials();
        $iri = $this->findIriBy(News::class, ['slug' => 'et-sit-enim-omnis']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
        // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::$container->get('doctrine')->getRepository(News::class)->findOneBy(['slug' => 'et-sit-enim-omnis'])
        );
    }
}
