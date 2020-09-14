<?php

namespace App\Tests\Controller\Api;

use App\Tests\Controller\TestClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    use TestClientTrait;
    use HalJsonTrait;

    public function testIndex()
    {
        $client = $this->getTestClient();

        $client->request('GET', 'api');
        $this->assertHalJson($client);
    }
}
