<?php

namespace App\Generator;

class Nonce implements GeneratorInterface
{
    public function create()
    {
        return base64_encode(random_bytes(20));
    }
}
