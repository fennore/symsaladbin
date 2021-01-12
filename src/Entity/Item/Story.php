<?php

namespace App\Entity\Item;

use App\Entity\Traits\SourceItem;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:'App\Repository\StoryRepository')]
#[ORM\Table(name:'story')]
final class Story extends Item implements SourceItemInterface
{
    use SourceItem;
}
