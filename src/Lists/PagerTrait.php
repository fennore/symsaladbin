<?php

namespace App\Lists;

use JMS\Serializer\Annotation as Serializer;

trait PagerTrait
{
    /**
     * @Serializer\Exclude
     */
    protected int $offset = 1;

    /**
     * @Serializer\Exclude
     */
    protected int $limit = 1;

    /**
     * @Serializer\Exclude
     */
    protected int $total = 0;

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getNext(): int
    {
        return min(
            floor($this->offset / $this->limit) * $this->limit + $this->limit,
            $this->getLast()
        );
    }

    public function getPrevious(): int
    {
        return max(
            floor($this->offset / $this->limit) * $this->limit - $this->limit,
            0
        );
    }

    public function getLast(): int
    {
        return max(
            floor(($this->total - 1) / $this->limit) * $this->limit,
            0
        );
    }
}
