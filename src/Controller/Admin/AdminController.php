<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\AbstractSmartController;
use App\Repository\LocationRepository;
use App\Repository\FileRepository;
use App\Importer\LocationImporter;
use App\Importer\DocumentImporter;
use App\Importer\ImageImporter;
use App\Handler\FileHandler;
use App\Handler\DirectionsHandler;

class AdminController extends AbstractSmartController
{
    /**
     * @Route("/admin", name="admin_overview")
     */
    public function overview()
    {
        // replace this line with your own code!
        return $this->smartRender('default/admin/overview.html.twig');
    }

    /**
     * @Route("/admin/route/{stage}", name="admin_route", requirements={"stage"="\d+"})
     */
    public function manageRoute(LocationRepository $locationRepository, $stage = 1)
    {
        return $this->smartRender('default/admin/route.html.twig', [
            'locationlist' => $locationRepository->getStageLocations($stage),
            'stagelist' => $locationRepository->getStageList(),
        ]);
    }

    /**
     * @Route("/admin/files", name="admin_files")
     */
    public function manageFiles(FileRepository $fileRepository)
    {
        return $this->smartRender('default/admin/files.html.twig', [
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

        return $this->redirectToRoute('admin_route');
    }

    /**
     * @Route("/admin/files/sync", name="admin_files_sync")
     */
    public function syncFiles(FileHandler $fileHandler)
    {
        $files = $fileHandler->syncSourceWithFileEntity();

        return $this->redirectToRoute('admin_files');
    }

    /**
     * @Route("/admin/route/sync", name="admin_route_sync")
     */
    public function syncRoute(LocationImporter $locImporter)
    {
        // Save gpx data as locations
        $locImporter->syncWithGpx();

        return $this->redirectToRoute('admin_route');
    }

    /**
     * @Route("/admin/stories/sync", name="admin_stories_sync")
     */
    public function syncStories(DocumentImporter $docImporter)
    {
        $docImporter->importDocuments();

        return $this->redirectToRoute('admin_overview');
    }

    /**
     * @Route("/admin/images/sync", name="admin_images_sync")
     */
    public function syncImages(ImageImporter $imgImporter)
    {
        $imgImporter->importImages();

        return $this->redirectToRoute('admin_overview');
    }
}
