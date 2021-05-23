<?php

namespace App\Entity\Item;

use App\Entity\{File,AbstractItem};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name:'timelineitem')]
#[ORM\HasLifecycleCallbacks]
class TimelineItem extends AbstractItem implements SourceItemInterface
{
    use ImageSourceItemTrait;

    public function getFile(): File
    {
        return $this->file;
    }

    public function getFileLocation(): string
    {
        return $this->file->getSource();
    }
}
