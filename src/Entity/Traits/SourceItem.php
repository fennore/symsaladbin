<?php

/*
  Note:
  mappedSuperclass + class table inheritance no worky!
  This does not work in combination with Class Inheritance
  As doctrine will read X extending SourceItem extending Item differently
  Looking for a non existing SourceItem table (report as bug?)
  MyTravel\Core\Model\SourceItem:
    type: mappedSuperclass
    oneToOne:
      file:
        targetEntity: MyTravel\Core\Model\File
        joinColumn:
          name: fileId
   */

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use App\Entity\File;

/**
 * Trait for Items that have a one-to-one relation with the File entity.
 */
trait SourceItem
{
    /**
     * @Serializer\Exclude
     * 
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     *
     * @var File Source File
     */
    protected $file;

    /**
     * Overwrite default Doctrine setFile,
     * So we can set the Item title according to file name when empty.
     *
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        // Update object title
        $this->setTitle($file->getFileName());

        $this->updated = $file->getLastModified();

        return $this;
    }

    abstract public function setTitle(string $title);

    /**
     * Remove File from Item.
     *
     * @return self
     */
    public function detachFile()
    {
        unset($this->file);

        return $this;
    }

    /**
     * Returns MIME types to match in a query.
     * Use a string for LIKE match, ex: text/%
     * Use an array for exact IN match.
     * The function returns self::MIMEMATCH by default.
     * Preferably just add class constant MIMEMATCH to your class using this trait.
     *
     * @return array
     */
    public static function matchMimeType(): array
    {
        return self::MIMEMATCH;
    }
}
