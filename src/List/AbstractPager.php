<?php

namespace App\Lists;

abstract class AbstractPager
{
    use PagerTrait;

    abstract public function showPage(): array;
}
