<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StoryControllerTest extends WebTestCase
{
    use TestClientTrait;
    use HalJsonTrait;

    public function testGetStories()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/stories');
        $this->assertHalJson($client);

        $client->request('GET', 'api/stories/0/1');
        $this->assertHalJson($client);

        $client->request('GET', 'api/stories/all');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }

    public function testCreateStories()
    {
        $client = $this->getTestClient();

        $client->request('POST', 'api/stories');
        $this->assertEquals(
            201,
            $client->getResponse()->getStatusCode(),
            'Verify response code 201'
        );

        $this->assertTrue(
            $client->getResponse()->headers->contains('Content-Type', 'application/json'),
            'Content type is JSON'
        );

        $client->request('POST', 'api/stories/all');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }

    public function testUpdateStories()
    {
        $client = $this->getTestClient();

        $client->request('PUT', 'api/stories');
        $this->assertTrue(
            $client->getResponse()->isEmpty(),
            'Verify response code 204'
        );

        $client->request('PUT', 'api/stories/all');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }

    public function testDeleteStories()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/stories');
        $this->assertTrue(
            $client->getResponse()->isEmpty(),
            'Verify response code 204'
        );
    }

    public function testClearStories()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/stories/all');
        $this->assertTrue(
            $client->getResponse()->isEmpty(),
            'Verify response code 204'
        );
    }
}
