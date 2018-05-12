<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\LocationRepository;
use App\Repository\FileRepository;
use App\Importer\LocationImporter;
use App\Handler\FileHandler;
use App\Handler\DirectionsHandler;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_overview", schemes="https")
     */
    public function overview()
    {
        // replace this line with your own code!
        return $this->render('default/admin/overview.html.twig');
    }

    /**
     * @Route("/admin/route/{stage}", name="admin_route", requirements={"stage"="\d+"})
     */
    public function manageRoute(LocationRepository $locationRepository, $stage = 1)
    {
        return $this->render('default/admin/route.html.twig', [
            'locationlist' => $locationRepository->getStageLocations($stage),
            'stagelist' => $locationRepository->getStageList(),
        ]);
    }

    /**
     * @Route("/admin/files", name="admin_files")
     */
    public function manageFiles(FileRepository $fileRepository)
    {
        return $this->render('default/admin/files.html.twig', [
            'filelist' => $fileRepository->getFiles(),
        ]);
    }

    /**
     * @Route("/admin/directions/calculate", name="admin_directions_calculate")
     */
    public function calculateDirections(DirectionsHandler $directionsHandler)
    {
        // Build encoded route
        $state = $directionsHandler->buildEncodedRoute();

        return $this->json(['status' => 'ok', 'state' => ['stage' => $state->getStage(), 'weight' => $state->getWeight()]]);
    }

    /**
     * @Route("/admin/files/sync", name="admin_files_sync")
     */
    public function syncFiles(FileHandler $fileHandler)
    {
        $files = $fileHandler->syncSourceWithFileEntity();

        return $this->json(['status' => 'ok', 'files-total' => iterator_count($files)]);
    }

    /**
     * @Route("/admin/route/sync", name="admin_route_sync")
     */
    public function syncRoute(LocationImporter $locationImporter)
    {
        // Save gpx data as locations
        $locationImporter->syncWithGpx();

        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/admin/stories/sync", name="admin_stories_sync")
     */
    public function syncStories()
    {
        return $this->json(['status' => 'ok']);
    }

    /**
     * @Route("/admin/images/sync", name="admin_images_sync")
     */
    public function syncImages()
    {
        return $this->json(['status' => 'ok']);
    }
}
