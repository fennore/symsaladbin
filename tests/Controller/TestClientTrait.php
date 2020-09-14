<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * This Trait is meant to be used for Controller tests.
 * Which implement the WebTestCase interface.
 * This trait therefor reflects the WebTestCase::createClient signature.
 */
trait TestClientTrait
{
    private function getTestClient(): KernelBrowser
    {
        return static::createClient(['environment' => 'test']);
    }

    abstract protected static function createClient(array $options = [], array $server = []);
}
