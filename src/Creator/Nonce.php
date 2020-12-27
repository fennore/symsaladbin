<?php

namespace App\Creator;

class Nonce implements CreatorInterface
{
    public function create()
    {
        return base64_encode(random_bytes(20));
    }
}
