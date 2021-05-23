<?php

namespace App\List;

interface PagerInterface
{
    public function getOffset(): int;
    public function getLimit(): int;
    public function getTotal(): int;
    public function getNext(): int;
    public function getPrevious(): int;
    public function getLast(): int;
    public function showPage(): array;
}
