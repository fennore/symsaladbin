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
        $this->assertResponseStatusCodeSame(
            200,
            'Assert status code for GET api/images'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', 'api/images/0/1');
        $this->assertResponseStatusCodeSame(
            200,
            'Assert status code for GET api/images/0/1'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', 'api/images/all');
        $this->assertResponseStatusCodeSame(
            405,
            'Assert status code for GET api/images/all'
        );
    }

    public function testClearImages()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/images/all');
        $this->assertResponseStatusCodeSame(
            403,
            'Assert status code for DELETE api/images/all without access rights'
        );

        $client->request('DELETE', 'api/images');
        $this->assertResponseStatusCodeSame(
            404,
            'Assert status code for DELETE api/images'
        );

        $this->loginTestUser($client);
        $client->request('DELETE', 'api/images/all');
        $this->assertResponseStatusCodeSame(
            204,
            'Assert status code for DELETE api/images/all with access rights'
        );
    }
}
