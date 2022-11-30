<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;

class CategoryApi extends ApiTestCase
{
    public function testCollectionGet(): void
    {
        self::createClient()->request('GET', '/api/categories');
        self::assertResponseStatusCodeSame(200);
        self::assertMatchesResourceCollectionJsonSchema(Category::class);
    }

    public function testGet(): void
    {
        $iri = $this->findIriBy(Category::class, ['id' => '59415216']);
        self::createClient()->request('GET', '/api/categories'.$iri);
        self::assertResponseStatusCodeSame(200);
    }
}
