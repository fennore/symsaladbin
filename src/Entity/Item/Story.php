<?php

namespace App\Entity\Item;

use App\Entity\Traits\SourceItem;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name:'story')]
class Story extends Item implements SourceItemInterface
{
    use SourceItem;
}
