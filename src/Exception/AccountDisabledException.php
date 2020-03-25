<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use App\Entity\User;

class AccountDisabledException extends AccountStatusException
{
    public function __construct(User $user)
    {
        $this->setUser($user);
    }

    public function getMessageKey(): string
    {
        return 'This user is not active.';
    }
}
