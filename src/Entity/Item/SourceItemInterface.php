<?php

namespace App\Entity\Item;

use App\Entity\File;

interface SourceItemInterface
{
    public function setFile(File $file);

    public function detachFile();

    public static function matchMimeType();
}
