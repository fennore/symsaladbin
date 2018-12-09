<?php

namespace App\Reader;

use App\Entity\File;
use App\Entity\Item\TimelineItem;

class SimpleImageReader
{
    public function getImageAsTimelineItem(File $file): TimelineItem
    {
        $timelineItem = new TimelineItem($file->getFileName(), '');
        $timelineItem->setFile($file);

        return $timelineItem;
    }
}
