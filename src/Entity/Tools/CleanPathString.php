<?php

namespace App\Entity\Tools;

/**
 * Creates a clean path string from a regular string.
 */
class CleanPathString
{
    private $originalString;

    private $replace;

    private $delimiter = '-';

    public function __construct(string $stringToConvert, array $replace = [], string $delimiter = '-')
    {
        $this
            ->setString($stringToConvert)
            ->setReplace($replace)
            ->delimiter($delimiter);
    }

    public function setString(string $stringToConvert)
    {
        $this->originalString = $stringToConvert;

        return $this;
    }

    public function setReplace(array $replace)
    {
        $this->replace = $replace;

        return $this;
    }

    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Get a clean path version of a string.
     *
     * @todo find something better (Symfony has path / url function?)
     *
     * @param string $str
     */
    private function getCleanPathString()
    {
        setlocale(LC_ALL, 'en_US.UTF8');

        $clean = $this->originalString;

        if (!empty($this->replace)) {
            $clean = str_replace((array) $this->replace, $this->delimiter, $clean);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = preg_replace("/[\/_|+ -]+/", $this->delimiter, $clean);
        $clean = strtolower(trim($clean, $this->delimiter));

        return $clean;
    }

    public function __toString()
    {
        return $this->getCleanPathString();
    }
}
