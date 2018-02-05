<?php

namespace App\Utils;

/**
 * Generator of simple data
 */
class Generator
{
    public function createNonce()
    {
        return base64_encode(random_bytes(20));
    }
}
