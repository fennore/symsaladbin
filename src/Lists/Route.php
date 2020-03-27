<?php

namespace App\Lists;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use App\Repository\LocationRepository;

/**
 * @todo Add relation condition for the update link to be only visible when accessible
 * @todo Add route clear conditional relation
 * @todo Only show links that actually work (no non existing stages)
 * 
 * Route wrapper for locations.
 *
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_route_locations",
 *         parameters = { "stage" = "expr(object.getStage())" },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "update",
 *     href = @Hateoas\Route(
 *         "api_route_update",
 *         parameters = { "currentStage" = "expr(object.getStage())" },
 *     ),
 *     attributes = { "method" = "POST" }
 * )
 * @Hateoas\Relation(
 *     name = "next",
 *     href = @Hateoas\Route(
 *         "api_route_locations",
 *         parameters = { "stage" = "expr(object.getNextStage())" },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "previous",
 *     href = @Hateoas\Route(
 *         "api_route_locations",
 *         parameters = { "stage" = "expr(object.getPreviousStage())" },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "first",
 *     href = @Hateoas\Route(
 *         "api_route_locations",
 *         parameters = { "stage" = 1 },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "last",
 *     href = @Hateoas\Route(
 *         "api_route_locations",
 *         parameters = { "stage" = "expr(object.getLastStage())" },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "locations",
 *     embedded = "expr(object.getLocations())"
 * )
 */
class Route
{
    private $stage;

    /**
     * @Serializer\Exclude
     *
     * @var LocationRepository
     */
    private $locationRepo;

    /**
     * @param int $stage Stage number of the route
     */
    public function __construct(int $stage, LocationRepository $locationRepo)
    {
        $this->stage = $stage;
        $this->locationRepo = $locationRepo;
    }

    public function getStage(): int
    {
        return $this->stage;
    }

    /**
     * @return Location[]
     */
    public function getLocations(): array
    {
        $locations = $this->locationRepo->getStageLocations($this->stage);
        $list = [];
        foreach ($locations as $row) {
            $list[] = $row[0];
        }

        return $list;
    }

    public function getNextStage(): int
    {
        return \min($this->stage + 1, $this->locationRepo->getLastStage());
    }

    public function getPreviousStage(): int
    {
        return \max($this->stage - 1, 1);
    }

    public function getLastStage(): int
    {
        return $this->locationRepo->getLastStage();
    }
}
