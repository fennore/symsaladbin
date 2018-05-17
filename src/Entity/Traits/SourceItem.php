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
use App\Entity\File;
use App\Entity\Item\Item;

/**
 * Trait for Items that have a one-to-one relation with the File entity.
 */
trait SourceItem
{
    /**
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

        // Update Item title
        if (empty($this->title) && $this instanceof Item) {
            $this->setTitle($file->getFileName());
        }
    }

    /**
     * Remove File from Item.
     */
    public function detachFile()
    {
        unset($this->file);
    }

    /**
     * Returns MIME types to match in a query.
     * Use a string for LIKE match, ex: text/%
     * Use an array for exact IN match.
     * The function returns self::MIMEMATCH by default.
     * Preferably just add class constant MIMEMATCH to your class using this trait.
     *
     * @return string|array
     */
    public static function matchMimeType()
    {
        return self::MIMEMATCH;
    }
}
