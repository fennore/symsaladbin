<?php

namespace App\Data;

use ArrayObject;
use App\Creator\NonceCreatorTrait;

/**
 * @see https://www.w3.org/TR/CSP3/
 */
class ContentSecurityPolicy implements ContentSecurityPolicyInterface
{

    use NonceCreatorTrait;
    
    private ArrayObject $csp;

    private string $nonce;

    private bool $useNonce = false;

    public function __construct()
    {
        $this->csp = new ArrayObject();
        $this->nonce = $this->createNonce();
    }

    public function get(): ArrayObject
    {
        return $this->csp;
    }

    /**
     * Set the security policy directive (overwrites previous).
     */
    public function set(ArrayObject $csp): void
    {
        $this->csp = $csp;
    }

    /**
     * Add a security policy directive.
     */
    public function add(string $directive, string $source): static
    {
        $this->csp[$directive][] = $source;

        return $this;
    }

    /**
     * Get the nonce value for in templates.
     * Automatically adds it to the content security policy on first usage.
     */
    public function getNonce(): string
    {
        if (!$this->useNonce) {
            $this->add('script-src', $this->nonce);
            $this->useNonce = true;
        }

        return $this->nonce;
    }

    public function __toString(): string
    {
        return array_reduce(
            array_keys($this->csp->getArrayCopy()), 
            fn ($policy, $directive) => "{$policy}{$directive} " . implode(' ', $this->csp[$directive]) . ';'
        );
    }
}
