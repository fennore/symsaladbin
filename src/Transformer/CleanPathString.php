<?php

namespace App\Transformer;

use Stringable;

/**
 * @todo use slugger from symfony/string
 * Creates a clean path string from a regular string.
 */
class CleanPathString implements Stringable
{
    private $originalString;

    private array $replace = [];

    private string $delimiter = '-';

    public function __construct(string $stringToConvert, array $replace = [], string $delimiter = '-')
    {
        $this
            ->setString($stringToConvert)
            ->setReplace($replace)
            ->setDelimiter($delimiter);
    }

    /**
     * Sets the original string to create a clean path string from.
     *
     * @param string $stringToConvert The original string
     */
    public function setString(string $stringToConvert): static
    {
        $this->originalString = $stringToConvert;

        return $this;
    }

    /**
     * Set the values to replace by delimiter.
     *
     * @param array $replace Array of values to replace with the delimiter
     */
    public function setReplace(array $replace): static
    {
        $this->replace = $replace;

        return $this;
    }

    /**
     * Set the delimiter value.
     * This value will be used to replace all undesired characters in a string.
     *
     * @param string $delimiter String delimiter. Defaults to '-'.
     */
    public function setDelimiter(string $delimiter = '-'): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Get a clean path version of a string.
     *
     * @todo find something better (Symfony has path / url function?)
     */
    private function getCleanPathString(): string
    {
        setlocale(LC_ALL, 'en_US.UTF8');

        $clean = $this->originalString;

        if (!empty($this->replace)) {
            $clean = str_replace((array) $this->replace, $this->delimiter, $clean);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
        // @todo the delimiter should be part of the ignored match
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = preg_replace("/[\/_|+ -]+/", $this->delimiter, $clean);
        $clean = strtolower(trim($clean, $this->delimiter));

        return $clean;
    }

    /**
     * Get the clean path string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getCleanPathString();
    }
}
