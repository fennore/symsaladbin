<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\{SavedStateRepository, LocationRepository};
use App\States\EncodedRoute;
use App\Lists\Route as LocationRoute;
use App\Entity\Location;
use App\Entity\Coordinate;


class ApiController extends AbstractController
{
    /**
     * @Route(
     *      "/api", 
     *      name="api_index", 
     *      schemes="https",
     *      defaults={"_format": "json"},
     *      requirements={"_format": "json"}
     * )
     */
    public function index()
    {
        return $this->json("hi");
    }
    
    /**
     * @Route("/api/encoded-route", name="api_encoded_route")
     */
    public function getEncodedRoute(SavedStateRepository $savedStateRepo)
    {
        //$routeState = $stateCtrl->get(self::STATEROUTE);
        $state = new EncodedRoute();
        $savedStateRepo->checkState($state);
        return $this->json($state->getRoute());
    }
    
    /**
     * @Route("/api/route/{stage}", name="api_route_locations", methods={"GET","HEAD"})
     */
    public function getLocations(LocationRepository $locationRepo, SerializerInterface $serializer, int $stage)
    {
        $route = new LocationRoute($stage, $locationRepo);
        $json = $serializer->serialize($route, 'json');
        return JsonResponse::fromJsonString($json);
    }
    
    /**
     * @Route("/api/route/{stage}", name="api_route_update", methods={"POST"})
     */
    public function updateRouteStage(Request $request, LocationRepository $locationRepo, int $stage)
    {
        $locations = \json_decode($request->getContent());
        foreach ($locations as $weight => $location) {
            \extract($location);
            $locationRepo->createLocation(new Location(new Coordinate((float) $coordinate['lat'], (float) $coordinate['lng']), $name, $stage, $weight));
        }
        return $this->json('[]', 204);
    }
}
