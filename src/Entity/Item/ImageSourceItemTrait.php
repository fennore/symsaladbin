<?php

namespace App\Entity\Item;

use App\Creator\ArrayToObject;
use App\Entity\File;
use Doctrine\ORM\Mapping as ORM;
use Throwable,ArrayObject;

trait ImageSourceItemTrait
{
    use SourceItem {
        setFile as sourceItemSetFile;
    }

    #[ORM\Column(type:'json', nullable:true)]
    protected array|ArrayObject $property;

    #[ORM\Column(type:'json', nullable:true)]
    protected array|ArrayObject $setting;

    /**
     * Overwrite SourceItem setFile.
     * Adding image exif data to property.
     * Setting Item created date to picture taken time.
     */
    public function setFile(File $file)
    {
        $this->sourceItemSetFile($file);

        try {
            if (null === $this->property) {
                $this->property = new ArrayObject(flags: ArrayObject::STD_PROP_LIST);
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

    public function getProperty(): ArrayObject
    {
        return $this->property;
    }

    public function getSetting(): ArrayObject
    {
        return $this->setting;
    }

    #[ORM\PostLoad]
    public function postLoad()
    {
        $arrayToObject = new ArrayToObject;
        $this->property = $arrayToObject->create($this->property);
        $this->setting = $arrayToObject->create($this->setting);
    }
}
