<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\LocationRepository;
use App\Handler\FileHandler;
use App\Importer\LocationImporter;
use App\Handler\DbBatchHandler;

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
     * @Route("/admin/route/sync", name="admin_route_sync")
     */
    public function syncRoute(DbBatchHandler $batchHandler, FileHandler $fileHandler, LocationImporter $locationImporter)
    {
        // Sync files in db
        $fileHandler->syncSourceWithFileEntity();
        // Make sure to have them written to db
        $batchHandler->cleanUpBatch();
        // Save gpx data as locations
        $locationImporter->syncWithGpx();
        return $this->json(['status' => 'ok']);
    }

}
