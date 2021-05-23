<?php

namespace App\Entity\Item;

use App\Entity\AbstractItem;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name:'story')]
class Story extends AbstractItem implements SourceItemInterface
{
    use SourceItemTrait;
}
