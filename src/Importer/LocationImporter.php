<?php

namespace App\Importer;

use App\Repository\FileRepository;
use App\Repository\LocationRepository;
use App\Reader\SimpleGpxFileReader;

/**
 * Importing Locations into the database.
 */
class LocationImporter
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @var SimpleGpxFileReader
     */
    private $gpxReader;

    /**
     * @param FileRepository      $fileRepository
     * @param LocationRepository  $locationRepository
     * @param SimpleGpxFileReader $gpxReader
     */
    public function __construct(FileRepository $fileRepository, LocationRepository $locationRepository, SimpleGpxFileReader $gpxReader)
    {
        $this->fileRepository = $fileRepository;
        $this->locationRepository = $locationRepository;
        $this->gpxReader = $gpxReader;
    }

    /**
     * Synchronize GPX files with Locations.
     * Keeps track of already read Files in application saveState.
     * Only reads unread files once,
     * because there is a one to many relation between GPX files and locations.
     * Each GPX file represents a new stage.
     */
    public function syncWithGpx()
    {
        // 1. Get highest existing stage number.
        // Take this straight from db since stages can be added manually.
        $lastStage = $this->locationRepository->getLastStage();
        // 2. Get savedState
//        $ctrlState = SavedStateController::create();
//        $savedState = $ctrlState->get(self::STATESYNC);
        // Detach savedState skipping flushes
//        ->detach($savedState);
        // 3. Add any locations from new files to subsequent stages.
        // - path is expected to be subpath of files directory
        //   anything else can be considered invalid anyway
        $files = $this->fileRepository->getFiles(['application/xml', 'text/xml']);
        foreach ($files as $row) {
            $file = $row[0];
            /*             * $duplicateCheck = in_array($file->getId(), $savedState->get('readFiles') ?? array());
              if ($duplicateCheck) {
              continue;
              } */
            foreach ($this->gpxReader->saveGpxAsLocations($file, ++$lastStage) as $location) {
                $this->locationRepository->createLocation($location);
            }
//          $savedState->add('readFiles', $file->id);
        }
    }
}
