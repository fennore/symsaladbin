<?php

namespace App\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * 
 */
class UserProvider implements UserProviderInterface {
  
    public function loadUserByUsername($username)
    {
    }
    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
    }
    public function supportsClass($class)
    {
    }
}
