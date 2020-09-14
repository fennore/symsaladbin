<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * This Trait is meant to be used for Controller tests.
 * Which implement the WebTestCase interface.
 * Its usage requires at least an assertTrue method with the PhpUnit Assert::assertTrue signature.
 */
trait HalJsonTrait
{
    abstract public function assertTrue($condition, string $message = ''): void;

    private function assertHalJson(KernelBrowser $client): void
    {
        $this->assertTrue(
            $client->getResponse()->isOK(),
            'Verify response code 200'
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains('Content-Type', 'application/hal+json'),
            'Content type is HAL+JSON'
        );
    }
}
