<?php

namespace App\Tests\Controller;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * This Trait is meant to be used for Controller tests.
 * Which implement the WebTestCase interface.
 * This trait therefor reflects the WebTestCase::createClient signature.
 */
trait TestClientTrait
{
    final protected function getTestClient(): KernelBrowser
    {
        return static::createClient(['environment' => 'test']);
    }

    final protected function loginTestUser(KernelBrowser $client): void
    {
        $role = new Role('ROLE_ADMIN');
        $user = new User('test', 'test', [$role]);
        $client->loginUser($user, 'session');
    }

    abstract protected static function createClient(array $options = [], array $server = []);
}
