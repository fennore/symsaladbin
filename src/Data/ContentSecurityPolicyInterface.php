<?php

namespace App\Data;

use ArrayObject, Stringable;

interface ContentSecurityPolicyInterface extends Stringable
{
    public function get(): ArrayObject;

    public function getNonce(): string;
    
    public function set(ArrayObject $csp): void;
    
    public function add(): static;
}
