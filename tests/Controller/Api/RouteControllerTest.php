<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteControllerTest extends WebTestCase
{
    use TestClientTrait;
    use HalJsonTrait;

    public function testEncodedRoute()
    {
        $client = $this->getTestClient();
        $client->request('GET', 'api/encoded-route');
        $this->assertHalJson($client);
    }

    public function testGetLocations()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/route/0');
        $this->assertHalJson($client);

        $client->request('GET', 'api/route/all');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }

    public function testUpdateLocations()
    {
        $client = $this->getTestClient();

        $client->request('PUT', 'api/route/0');
        $this->assertTrue(
            $client->getResponse()->isEmpty(),
            'Verify response code 204'
        );
        $client->request('PUT', 'api/route/all');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
    }

    public function testClearRoute()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/route/0');
        $this->assertTrue(
            $client->getResponse()->isNotFound(),
            'Check response on invalid path'
        );
        $client->request('DELETE', 'api/route/all');
        $this->assertTrue(
            $client->getResponse()->isEmpty(),
            'Verify response code 204'
        );
    }
}
