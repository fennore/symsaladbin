<?php

namespace App\Entity\Traits;

use Throwable;
use App\Entity\File;

trait ImageSourceItem
{
    use SourceItem {
        setFile as sourceItemSetFile;
    }

    /**
     * @ORM\Column(type="json", nullable=true);
     */
    protected $property;

    /**
     * @ORM\Column(type="json", nullable=true);
     */
    protected $setting;

    /**
     * Overwrite SourceItem setFile.
     * Adding image exif data to property.
     * Setting Item created date to picture taken time.
     *
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->sourceItemSetFile($file);
        // Add exif data
        try {
            if (null === $this->property) {
                $this->property = (object) [];
            }
            $exif = exif_read_data($file->getSource(), 'FILE,COMPUTED', true, false);
            $check = ['FILE' => null, 'COMPUTED' => null];
            $this->property->exif = array_intersect_key($exif, $check);
            // @todo put lat and lng data from exif into settings
        } catch (Throwable $ex) {
            // @todo Warning for exif data failure
        }
        $this->content = $exif['COMPUTED']['UserComment'] ?? null;
        // Created date should match date of picture taken
        $timeOfCreation = $exif['EXIF']['DateTimeOriginal'] ?? $exif['IFD0']['DateTime'] ?? null;
        if (null !== $timeOfCreation) {
            $this->created = strtotime($timeOfCreation);
        }
    }

    public function getProperty(): object
    {
        return (object) $this->property;
    }

    public function getSetting(): object
    {
        return (object) $this->setting;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->property = (object) $this->property;
        $this->setting = (object) $this->setting;
    }
}
