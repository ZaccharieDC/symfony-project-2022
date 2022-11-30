<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Advert;
use App\Entity\Category;

class AdvertApi extends ApiTestCase
{
    public function testCollectionGet(): void
    {
        self::createClient()->request('GET', '/api/adverts');
        self::assertResponseStatusCodeSame(200);
        self::assertMatchesResourceCollectionJsonSchema(Advert::class);
    }

    public function testGet(): void
    {
        $iri = $this->findIriBy(Advert::class, ['id' => '418941516']);
        self::createClient()->request('GET', '/api/adverts'.$iri);
        self::assertResponseStatusCodeSame(200);
    }

    public function testCreateBook(): void
    {
        $response = static::createClient()->request('POST', '/api/adverts', ['json' => [
            'title' => 'The Handmaid\'s Tale',
            'content' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            'author' => 'Margaret Atwood',
            'email' => 'user@example.com',
            'category' => '/api/categories/1',
            'price' => 100,
            'createdAt' => '2022-11-30T11:09:32.786Z',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Advert::class);
    }
}