<?php

namespace App\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use App\Reader\DirectoryReader;

/**
 * File directory namer for Vich Uploader bundle.
 *
 * @see https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/directory_namer/howto/create_a_custom_directory_namer.md
 */
class FileSubdirectoryNamer implements DirectoryNamerInterface
{
    /**
     * @var DirectoryReader
     */
    private $directoryReader;

    /**
     * @param DirectoryReader $directoryReader
     */
    public function __construct(DirectoryReader $directoryReader)
    {
        $this->directoryReader = $directoryReader;
    }

    /**
     * {@inheritdoc}
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return $this->determineSubDirectoryForMimeType($object->getMimeType()).'/';
    }

    /**
     * Determines the file subdirectory for a file to save to,
     * according to its MIME type.
     *
     * @param string $mimeType Given MIME type
     *
     * @return string
     */
    private function determineSubDirectoryForMimeType(string $mimeType): string
    {
        $getRelative = true;

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/png':
                return $this->directoryReader->getImagesDirectory($getRelative);
            case 'application/xml':
            case 'text/xml':
                return $this->directoryReader->getGpxDirectory($getRelative);
            case 'application/vnd.oasis.opendocument.text':
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return $this->directoryReader->getStoriesDirectory($getRelative);
            default:
                return '';
        }
    }
}
