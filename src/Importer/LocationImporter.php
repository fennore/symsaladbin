<?php

namespace App\Importer;

use App\Repository\FileRepository;
use App\Repository\LocationRepository;
use App\Repository\SavedStateRepository;
use App\Reader\SimpleGpxFileReader;
use App\States\ImportLocationState;

/**
 * Importing Locations into the database.
 */
class LocationImporter
{
    /**
     * @var SavedStateRepository
     */
    private $savedStateRepository;

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
    public function __construct(SavedStateRepository $savedStateRepo, FileRepository $fileRepo, LocationRepository $locationRepo, SimpleGpxFileReader $gpxReader)
    {
        $this->savedStateRepository = $savedStateRepo;
        $this->fileRepository = $fileRepo;
        $this->locationRepository = $locationRepo;
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
        // 2. Use State to keep track and avoid doubles
        $state = new ImportLocationState();
        $savedState = $this->savedStateRepository->checkState($state);
        // 3. Add any locations from new files to subsequent stages.
        $files = $this->fileRepository->getFiles(['application/xml', 'text/xml']);
        foreach ($files as $row) {
            $file = $row[0];
            if (in_array($file->getId(), $state->getReadFiles())) {
                continue; // Skip
            }
            foreach ($this->gpxReader->saveGpxAsLocations($file, ++$lastStage) as $location) {
                $this->locationRepository->createLocation($location);
            }
            $state->addReadFile($file->getId());
        }
        // 4. Make sure to merge before updating saved state
        $mergedSavedState = $this->savedStateRepository->mergeSavedState($savedState);
        $this->savedStateRepository->updateSavedState($mergedSavedState);
    }
}
