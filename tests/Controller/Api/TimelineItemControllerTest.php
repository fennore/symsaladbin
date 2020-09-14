<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TimelineItemControllerTest extends WebTestCase
{
    use TestClientTrait;
    use HalJsonTrait;

    public function testGetImages()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/images');
        $this->assertHalJson($client);

        $client->request('GET', 'api/images/0/1');
        $this->assertHalJson($client);

        $client->request('GET', 'api/images/all');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }

    public function testClearImages()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/images/all');
        $this->assertTrue($client->getResponse()->isEmpty());

        $client->request('DELETE', 'api/images');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }
}
