<?php

namespace App\Entity\Item;

use App\Entity\File;
use App\Entity\Traits\ImageSourceItem;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer; // note: bug? This is required as the annotation doesn't seem to work for multi level trait usages.

/**
#[ORM\Entity(repositoryClass="App\Repository\TimelineItemRepository")]
#[ORM\Table(name="timelineitem")]
#[ORM\HasLifecycleCallbacks()]
 */
class TimelineItem extends Item implements SourceItemInterface
{
    use ImageSourceItem;

    /**
    #[Serializer\Exclude Required for cs fixer... :'(]
     * For now only jpeg and png support.
     */
    const MIMEMATCH = [
        'image/jpeg',
        'image/png',
    ];

    public function getFile(): File
    {
        return $this->file;
    }

    public function getFileLocation(): string
    {
        return $this->file->getSource();
    }
}
