<?php

namespace App\Tests;

use App\Entity\File;

/**
 * Test file entity for testing functionality without requirement of existing files.
 */
class DummyFile extends File
{
    private $mimeType;

    /**
     * Overwrite constructor,
     * so we can use non existing files as data for testing.
     */
    public function __construct()
    {
    }

    /**
     * Set the Mime Type.
     * We don't want this possibility for the real file entity.
     *
     * @param string $mimeType
     */
    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * Need to overwrite getMimeType
     * because we are dealing with a private property.
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
