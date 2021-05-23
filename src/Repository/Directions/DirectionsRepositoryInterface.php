<?php

namespace App\Repository\Directions;

use Generator;
use App\Entity\Directions;

interface DirectionsRepositoryInterface
{
    public function persist(Directions ...$directoins): void;
    public function remove(Directions ...$directoins): void;
    public function findByStage(int $stage): Generator;
    public function clearStage(int $stage): void;
}
