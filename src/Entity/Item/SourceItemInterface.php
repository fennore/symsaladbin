<?php

namespace App\Entity\Item;

use App\Entity\File;

interface SourceItemInterface
{
    public function setFile(File $file): void;

    public function detachFile(): void;
}
