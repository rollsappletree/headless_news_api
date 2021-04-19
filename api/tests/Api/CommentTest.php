<?php
// api/tests/CommentsTest.php

namespace App\Tests\Api;

use App\Entity\Comment;
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Tests\AbstractTest;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class CommentTest extends AbstractTest
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    private NewsRepository $newsRepository;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->newsRepository = $kernel->getContainer()
                                      ->get('doctrine')
                                      ->getManager()
                                      ->getRepository(News::class);
    }

    public function testGetCollection(): void
    {
        //get random news:
        $news = $this->newsRepository->findOneBy(['slug' => 'et-sit-enim-omnis']);

        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClientWithCredentials()->request('GET', sprintf('/news/%s/comments', $news->getId()));

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/contexts/Comment',
            '@id' => sprintf('/news/%s/comments', $news->getId()),
            '@type' => 'hydra:Collection',
        ]);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Comment::class);
    }

    public function testCreateComments(): void
    {
        //get random news:
        $news = $this->newsRepository->findOneBy(['slug' => 'et-sit-enim-omnis']);

        $response = $this->createClientWithCredentials()->request('POST', sprintf('/news/%s/comments', $news->getId()), ['json' => [
            'text' => 'COMMENT: Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/Comment',
            '@type' => 'Comment',
            'text' => 'COMMENT: Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
        ]);
        $this->assertMatchesRegularExpression('~^/comments/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Comment::class);
    }

    public function testUpdateComments(): void
    {
        //get random news:
        /** @var News $news */
        $news    = $this->newsRepository->findOneBy(['slug' => 'et-sit-enim-omnis']);
        /** @var Comment $comment */
        $comment = $news->getComments()->first();
        $iri = sprintf('/comments/%d', $comment->getId());

        $client = $this->createClientWithCredentials();


        $client->request('PUT', $iri, ['json' => [
            'text' => 'COMMENT MODIFIED: Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'text' => 'COMMENT MODIFIED: Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
        ]);
    }

    public function testDeleteComments(): void
    {
        $client = $this->createClientWithCredentials();
        /** @var News $news */
        $news    = $this->newsRepository->findOneBy(['slug' => 'et-sit-enim-omnis']);
        /** @var Comment $comment */
        $comment = $news->getComments()->first();
        $iri = sprintf('/comments/%d', $comment->getId());

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
        // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::$container->get('doctrine')->getRepository(Comment::class)->findOneBy(['id' => $comment->getId()])
        );
    }
}
