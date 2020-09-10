<?php

namespace App\Utils;

/**
 * @todo Utils is too generalistic, rename to Generator\SimpleGenerator
 * Generator of simple data.
 */
class Generator
{
    public function createNonce()
    {
        return base64_encode(random_bytes(20));
    }
}
