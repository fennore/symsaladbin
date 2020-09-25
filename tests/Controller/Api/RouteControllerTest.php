<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteControllerTest extends WebTestCase
{
    use TestClientTrait;

    public function testEncodedRoute()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/encoded-route');
        $this->assertResponseStatusCodeSame(
            200,
            'Assert status code for GET api/encoded-route'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');
    }

    public function testGetLocations()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api/route/0');
        $this->assertResponseStatusCodeSame(
            200,
            'Assert status code for GET api/route/0'
        );
        $this->assertResponseHeaderSame('Content-Type', 'application/hal+json');

        $client->request('GET', 'api/route/all');
        $this->assertResponseStatusCodeSame(
            405,
            'Assert status code for GET api/route/all'
        );
    }

    public function testUpdateLocations()
    {
        $client = $this->getTestClient();

        $client->request('PUT', 'api/route/0');
        $this->assertResponseStatusCodeSame(
            403,
            'Assert status code for PUT api/route/0 without access rights'
        );

        $client->request('PUT', 'api/route/all');
        $this->assertResponseStatusCodeSame(
            405,
            'Assert status code for PUT api/route/all'
        );

        $this->loginTestUser($client);
        $client->request('PUT', 'api/route/0');
        $this->assertResponseStatusCodeSame(
            400,
            'Assert status code for PUT api/route/0 with access rights and empty data'
        );

        $content = json_encode([
            'coordinate' => ['lat' => 1, 'lng' => 1],
            'name' => 'my name',
            'stage' => 0,
            'status' => 1,
        ]);
        $client->request('PUT', 'api/route/0', [], [], [], $content);
        $this->assertResponseStatusCodeSame(
            204,
            'Assert status code for PUT api/route/0 with access rights and correct data'
        );
    }

    public function testClearRoute()
    {
        $client = $this->getTestClient();

        $client->request('DELETE', 'api/route/0');
        $this->assertResponseStatusCodeSame(
            404,
            'Assert status code for DELETE api/route/0'
        );

        $client->request('DELETE', 'api/route/all');
        $this->assertResponseStatusCodeSame(
            403,
            'Assert status code for DELETE api/route/all without access rights'
        );

        $this->loginTestUser($client);
        $client->request('DELETE', 'api/route/all');
        $this->assertResponseStatusCodeSame(
            204,
            'Assert status code for DELETE api/route/all with access rights'
        );
    }
}
