<?php

namespace App\Controller\Api;

use App\Entity\Coordinate;
use App\Entity\Location;
use App\Lists\Route as LocationRoute;
use App\Repository\DirectionsRepository;
use App\Repository\LocationRepository;
use App\Repository\SavedStateRepository;
use App\States\DirectionsState;
use App\States\EncodedRoute;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends AbstractController
{
    /**
     * @Route("/api/encoded-route", name="api_encoded_route")
     */
    public function getEncodedRoute(SavedStateRepository $savedStateRepo)
    {
        $state = new EncodedRoute();
        $savedStateRepo->checkState($state);

        return $this->json($state->getRoute());
    }

    /**
     * @Route(
     *      "/api/route/{stage}",
     *      name="api_route_locations",
     *      methods={"GET","HEAD"},
     *      requirements={"stage"="\d+"})
     */
    public function getLocations(
        LocationRepository $locationRepo,
        SerializerInterface $serializer,
        int $stage)
    {
        $route = new LocationRoute($stage, $locationRepo);
        $json = $serializer->serialize($route, 'json');

        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route(
     *      "/api/route/{currentStage}",
     *      name="api_route_update",
     *      methods={"PUT"},
     *      requirements={"currentStage"="\d+"})
     */
    public function updateRouteStage(
        Request $request,
        LocationRepository $locationRepo,
        DirectionsRepository $directionsRepo,
        SavedStateRepository $savedStateRepo,
        int $currentStage)
    {
        $locations = json_decode($request->getContent(), true);
        // Remove calculated Directions from stage
        $directionsRepo->clearStage($currentStage);
        // Remove all Locations from stage
        $locationRepo->clearStage($currentStage);

        // Saved state set directions calculation back to this stage ? => no -> too limited
        // NEW => introducing updated stage application state
        $directionsState = new DirectionsState();
        $savedState = $savedStateRepo->checkState($directionsState);
        $directionsState->addPendingUpdate($currentStage);

        // Write new Locations to stage
        foreach ($locations as $weight => $location) {
            extract($location);
            $directionsState->addPendingUpdate($stage);
            $locationRepo->createLocation(new Location(new Coordinate((float) $coordinate['lat'], (float) $coordinate['lng']), $name, $stage, $weight, $status));
        }
        $savedStateRepo->updateSavedState($savedStateRepo->mergeSavedState($savedState));

        // Saved state empty stage route ? => no replace old when updated route has been fully calculated
        // Only if Locations are empty, remove encoded route as well
        if (empty($locations)) {
            $route = new EncodedRoute();
            $savedState = $savedStateRepo->checkState($route);
            $route->setStage($currentStage, []);
            $savedStateRepo->updateSavedState($savedState);
        }

        return JsonResponse::create(null, 204);
    }

    /**
     * Clear the route data.
     *
     * @Route("/api/route/all", name="api_route_clear", methods={"DELETE"})
     */
    public function clearRoute(
        LocationRepository $locationRepo,
        DirectionsRepository $directionsRepo,
        SavedStateRepository $savedStateRepo)
    {
        // empty location list
        $locationRepo->truncateTable();
        // empty directions list
        $directionsRepo->truncateTable();
        // empty encoded route
        $state = new EncodedRoute();
        $savedStateRepo->deleteSavedState($savedStateRepo->checkState($state));
        // reset directions state
        $state = new DirectionsState();
        $savedStateRepo->deleteSavedState($savedStateRepo->checkState($state));

        return JsonResponse::create(null, 204);
    }
}
