<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testGetLocations()
    {
        $client = static::createClient();

        $client->request('GET', 'api/route/1');
        
        // Assert status code
        // $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert that the "Content-Type" header is "application/hal+json"
        /*$this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on success
        );*/
        // Assert response structure
        //...
    }
}
