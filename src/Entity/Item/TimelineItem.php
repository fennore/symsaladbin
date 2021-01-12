<?php

namespace App\Entity\Item;

use App\Entity\File;
use App\Entity\Traits\ImageSourceItem;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:'App\Repository\TimelineItemRepository')]
#[ORM\Table(name:'timelineitem')]
#[ORM\HasLifecycleCallbacks]
class TimelineItem extends Item implements SourceItemInterface
{
    use ImageSourceItem;

    public function getFile(): File
    {
        return $this->file;
    }

    public function getFileLocation(): string
    {
        return $this->file->getSource();
    }
}
