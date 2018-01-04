<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File as BaseFile;
use Symfony\Component\HttpFoundation\File\UploadedFile; // Uploaded file will be of this type instead


/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="file", indexes={@Index(name="file_select", columns={"mime_type", "path"})})
 * @Vich\Uploadable
 */
class File
{

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="files", fileNameProperty="fileName", mimeType="mimeType")
     * 
     * @var File
     */
    private $file;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="filename", type="string", unique=true)
     */
    private $fileName;

    /**
     * @ORM\Column(type="string")
     */
    private $path;

    /**
     * @ORM\Column(name="mime_type",type="string")
     */
    private $mimeType;

    /**
     * @ORM\Column(name="last_modified", type="integer", options={"unsigned":true})
     */
    private $lastModified;

    /**
     * In some cases we want item info with the file.
     * @todo check if we should use unilateral Doctrine for this instead.
     * @var Item
     */
    private $item;

    /**
     * Holds the file data.
     * For now not using it.
      type: blob
      nullable: true
     * @var object
     */
    private $data;

    public function __construct(BaseFile $file)
    {
        $this->fileName = $file->getFilename();
        $this->mimeType = $file->getMimeType();
        $this->setFile($file);
        //$this->data = $fileInfo->getContents();
    }
    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param BaseFile|UploadedFile $image
     */
    public function setFile(BaseFile $file): void
    {
        $this->file = $file;
        $this->path = str_replace('\\', '/', $file->getPath()); // Always use / for directory separator
        $this->lastModified = $file->getMTime();
    }
       
    /**
     * Function to fix legacy paths saved in database
     */
    public function cleanPaths(): void
    {
        $this->path = str_replace('\\', '/', $this->path);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getPath(): string
    {
        return $this->path;
    }
    
    public function getSource(): string
    {
        return $this->path.'/'.$this->fileName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getLastModified(): int
    {
        return $this->lastModified;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
