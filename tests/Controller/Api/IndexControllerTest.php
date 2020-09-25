<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    use TestClientTrait;

    public function testIndex()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api');
        $this->assertResponseStatusCodeSame(
            200,
            'Assert status code for GET api'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');
    }
}
