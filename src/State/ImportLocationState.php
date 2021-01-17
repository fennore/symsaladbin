<?php

namespace App\States;

class ImportLocationState implements StateInterface
{
    const KEY = 943;

    private $readFiles = [];

    public function getKey(): int
    {
        return self::KEY;
    }

    public function getReadFiles(): array
    {
        return $this->readFiles;
    }

    public function addReadFile(int $fileId)
    {
        array_push($this->readFiles, $fileId);
    }
}
