<?php

namespace App\Entity\Item;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\ImageSourceItem;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimelineItemRepository")
 * @ORM\Table(name="timelineitem")
 * @ORM\HasLifecycleCallbacks()
 */
class TimelineItem extends Item implements SourceItemInterface
{
    use ImageSourceItem;

    /**
     * For now only jpeg and png support.
     */
    const MIMEMATCH = [ 
    	'image/jpeg',
    	'image/png',
    ];
}
