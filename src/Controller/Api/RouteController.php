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

        return $this->json(
            $state->getRoute(),
            JsonResponse::HTTP_OK,
            ['Content-Type' => 'application/hal+json']
        );
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

        return JsonResponse::fromJsonString(
            $json,
            JsonResponse::HTTP_OK,
            ['Content-Type' => 'application/hal+json']
        );
    }

    /**
     * @Route(
     *      "/api/route/{currentStage}",
     *      name="api_route_update",
     *      methods={"PUT"},
     *      requirements={"currentStage"="\d+"})
     */
    public function updateLocations(
        Request $request,
        LocationRepository $locationRepo,
        DirectionsRepository $directionsRepo,
        SavedStateRepository $savedStateRepo,
        int $currentStage)
    {
        $locations = json_decode($request->getContent(), true);

        // An update requires data, otherwise it would be a removal and should be a DELETE
        if (empty($locations)) {
            return JsonResponse::create(null, JsonResponse::HTTP_BAD_REQUEST);
        }

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
        foreach ($locations as $weight => [
            'coordinate' => $coordinate,
            'name' => $name,
            'stage' => $stage,
            'status' => $status,
        ]) {
//            $directionsState->addPendingUpdate($stage); Only do this if you make sure the weights are set correctly for their logical position in the previous or next stage (last and first respectively)
            $locationRepo->createLocation(new Location(new Coordinate((float) $coordinate['lat'], (float) $coordinate['lng']), $name, $stage, $weight, $status));
        }
        $savedStateRepo->updateSavedState($savedStateRepo->mergeSavedState($savedState));

        // Saved state empty stage route ? => no replace old when updated route has been fully calculated

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
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
        $encodedRoute = new EncodedRoute();
        $savedStateRepo->deleteSavedState($savedStateRepo->checkState($encodedRoute));
        // reset directions state
        $directionsState = new DirectionsState();
        $savedStateRepo->deleteSavedState($savedStateRepo->checkState($directionsState));

        return new JsonResponse(null, 204);
    }
}
