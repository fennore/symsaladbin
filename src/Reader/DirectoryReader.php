<?php

namespace App\Reader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Returns App directory information or content using the Finder Component.
 */
class DirectoryReader
{
    const DIRECTORYNAME_FILES = 'app.files.directory';

    const SUBDIRECTORYNAME_GPX = 'app.files.subdir.gpx';

    const SUBDIRECTORYNAME_STORIES = 'app.files.subdir.stories';

    const SUBDIRECTORYNAME_IMAGES = 'app.files.subdir.images';

    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get all files in the files directory.
     *
     * @return Finder
     */
    public function getAllFiles(): Finder
    {
        return Finder::create()->files()
            ->followLinks() // Follow symbolic links!
            ->in($this->getFilesDirectory());
    }

    public function getFilesDirectory(): string
    {
        return $this->container->getParameter(self::DIRECTORYNAME_FILES) ?? '%kernel.project_dir%/var/files';
    }
}
