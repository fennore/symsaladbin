<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StoryControllerTest extends WebTestCase
{
    use TestClientTrait;

    public function testGetStories()
    {
        $client = $this->getTestClient();

        $client->request('GET', '/api/stories');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Assert status code for GET api/stories'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', '/api/stories/0/1');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Assert status code for GET api/stories/0/1'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', '/api/stories/all');
        $this->assertEquals(
            405,
            $client->getResponse()->getStatusCode(),
            'Assert status code for GET api/stories/all'
        );
    }

    public function testCreateStories()
    {
        $client = $this->getTestClient();

        $client->request('POST', '/api/stories');
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Assert status code for POST api/stories without access rights'
        );

        $client->request('POST', '/api/stories/all');
        $this->assertEquals(
            405,
            $client->getResponse()->getStatusCode(),
            'Assert status code for POST api/stories/all'
        );

        $this->loginTestUser($client);
        $client->request('POST', '/api/stories');
        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            'Assert status code for POST api/stories with access rights and no data'
        );

        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');
    }

    public function testUpdateStories()
    {
        $client = $this->getTestClient();

        $client->request('PUT', '/api/stories');
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Assert status code for PUT api/stories without access rights'
        );

        $client->request('PUT', '/api/stories/all');
        $this->assertEquals(
            405,
            $client->getResponse()->getStatusCode(),
            'Assert status code for PUT api/stories/all'
        );

        $this->loginTestUser($client);
        $client->request('PUT', '/api/stories');
        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            'Assert status code for PUT api/stories with access rights and no data'
        );
    }

    public function testDeleteStories()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', '/api/stories');
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/stories without access rights'
        );

        $this->loginTestUser($client);
        $client->request('DELETE', '/api/stories');
        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/stories with access rights and no data'
        );
    }

    public function testClearStories()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', '/api/stories/all');
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/stories/all without access rights'
        );

        $this->loginTestUser($client);
        $client->request('DELETE', '/api/stories/all');
        $this->assertEquals(
            204,
            $client->getResponse()->getStatusCode(),
            'Assert status code for DELETE api/stories/all with access rights'
        );
    }
}
