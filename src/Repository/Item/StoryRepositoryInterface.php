<?php

namespace App\Repository\Item;

use App\Entity\Item\Story;

interface StoryRepositoryInterface
{
    public function persist(Story ...$story);
    public function remove(Story ...$story);
}
