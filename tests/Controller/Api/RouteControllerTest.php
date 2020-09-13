<?php

namespace App\Tests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteControllerTest extends WebTestCase
{
    private const TEST_STAGE = 3;

    public function testEncodedRoute()
    {
        $client = $this->getTestClient();
        $client->request('GET', 'api/encoded-route');
        $this->assertHalJson($client);
    }

    public function testGetLocations()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/route/'.self::TEST_STAGE);
        $this->assertHalJson($client);

        $client->request('GET', 'api/route/bla');
        $this->assertTrue(
            $client->getResponse()->isNotFound()
        );
    }

    public function testUpdateLocations()
    {
        $client = $this->getTestClient();

        $client->request('PUT', 'api/route/'.self::TEST_STAGE);
        $this->assertTrue(
            $client->getResponse()->isEmpty()
        );
        $client->request('PUT', 'api/route/bla');
        $this->assertTrue(
            $client->getResponse()->isNotFound()
        );
    }

    public function testClearRoute()
    {
        $client = $this->getTestClient();

        $client->request('DEL', 'api/route/0');
        $this->assertTrue(
            $client->getResponse()->isNotFound()
        );
        $client->request('DEL', 'api/route/all');
        $this->assertTrue(
            $client->getResponse()->isEmpty()
        );
    }

    private function assertHalJson(KernelBrowser $client): void
    {
        $this->assertTrue(
            $client->getResponse()->isOK()
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains('Content-Type', 'application/hal+json')
        );
    }

    private function getTestClient(): KernelBrowser
    {
        return static::createClient(['environment' => 'test']);
    }
}
