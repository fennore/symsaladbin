<?php

namespace App\Data;

use App\Creator\Nonce;

/**
 * @see https://www.w3.org/TR/CSP3/
 */
class ContentSecurityPolicy
{
    /**
     * @var array
     */
    private $csp = [];

    /**
     * @var string
     */
    private $nonce;

    /**
     * @var bool
     */
    private $useNonce = false;

    public function __construct(Nonce $nonce)
    {
        $this->nonce = $nonce->create();
    }

    /**
     * Set a security policy directive.
     */
    public function set(string $directive, array $sources)
    {
        $this->csp[$directive] = $sources;

        return $this;
    }

    /**
     * Add to a security policy directive.
     */
    public function add(string $directive, string $source)
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
        }

        return $this->nonce;
    }

    /**
     * Get the string formatted policy for in the header.
     */
    public function getPolicy(): ?string
    {
        $csp = $this->csp;

        return array_reduce(array_keys($this->csp), function ($policy, $directive) use ($csp) {
            return $policy.$directive.' '.implode(' ', $csp[$directive]).'; ';
        });
    }
}
