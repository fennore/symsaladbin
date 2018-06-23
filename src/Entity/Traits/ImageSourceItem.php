<?php

namespace App\Entity\Traits;

use Throwable;
use App\Entity\File;

trait ImageSource {

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
  public function setFile(File $file) {
    $this->sourceItemSetFile($file);
    // Add exif data
    try {
      if(!isset($this->property)) {
        $this->property = (object) array();
      }
      $exif = @\exif_read_data($this->file->getFullSource(), 'FILE,COMPUTED', true, false);
      $check = array('FILE' => NULL, 'COMPUTED' => NULL);
      $this->property->exif = array_intersect_key($exif, $check);
    } catch (Throwable $ex) {
      // @todo Warning for exif data failure
    }
    // Created date should match date of picture taken
    if (isset($exif['EXIF']['DateTimeOriginal'])) {
      $this->created = strtotime($exif['EXIF']['DateTimeOriginal']);
    } else if (isset($exif['IFD0']['DateTime'])) {
      $this->created = strtotime($exif['IFD0']['DateTime']);
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
  public function postLoad() {
    $this->property = (object) $this->property;
    $this->setting = (object) $this->setting;
  }
}
