<?php

namespace App\Entity\Item;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\SourceItem;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoryRepository")
 * @ORM\Table(name="story")
 */
class Story extends Item implements SourceItemInterface
{
    use SourceItem;

    const MIMEMATCH = [ 
        'application/vnd.oasis.opendocument.text',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
}
