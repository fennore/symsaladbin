<?php

namespace App\Creator;

trait NonceCreatorTrait
{
    private function createNonce(): string
    {
        return base64_encode(random_bytes(20));
    }
}
