<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\AbstractItem;

interface ItemRepositoryInterface
{
    public function getTimelinePaginator();
}
