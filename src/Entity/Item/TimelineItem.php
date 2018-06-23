<?php

namespace App\Entity\Item;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimelineItemRepository")
 * @ORM\Table(name="timelineitem")
 * @ORM\HasLifecycleCallbacks()
 */
class TimelineItem extends Item implements SourceItemInterface
{
    /**
     * For now only jpeg and png support.
     */
    const MIMEMATCH = array(
    'image/jpeg',
    'image/png',
  );
}
