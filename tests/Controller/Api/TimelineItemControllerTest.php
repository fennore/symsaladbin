<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TimelineItemControllerTest extends WebTestCase
{
    use TestClientTrait;

    public function testGetImages()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/images');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Assert status code for GET api/images'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', 'api/images/0/1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Assert status code for GET api/images/0/1'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', 'api/images/all');
        $this->assertEquals(
            405,
            $client->getResponse()->getStatusCode(),
            'Assert status code for GET api/images/all'
        );
    }

    public function testClearImages()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/images/all');
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/images/all without access rights'
        );

        $client->request('DELETE', 'api/images');
        $this->assertEquals(
            404,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/images'
        );

        $this->loginTestUser($client);
        $client->request('DELETE', 'api/images/all');
        $this->assertEquals(
            204,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/images/all with access rights'
        );
    }
}
