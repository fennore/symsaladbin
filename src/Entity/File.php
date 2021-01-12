<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\{File as BaseFile,UploadedFile};
use Vich\UploaderBundle\Mapping\Annotation as Vich; // Uploaded file will be of this type instead

#[ORM\Entity(repositoryClass:'App\Repository\FileRepository')]
#[ORM\Table(name:'file', indexes:[#[ORM\Index(name:'file_select', columns:['mime_type', 'path'])]])]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
final class File
{

    #[Vich\UploadableField(mapping:'files', fileNameProperty:'fileName', mimeType:'mimeType')]
    private BaseFile $file;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[ORM\Column(name:'filename', type:'string')]
    private string $fileName;

    #[ORM\Column(type:'string')]
    private string $path;

    private string $source;

    #[ORM\Column(name:'mime_type',type:'string')]
    private string $mimeType;

    #[ORM\Column(name:'last_modified', type:'integer', options:['unsigned' => true])]
    private int $lastModified;

    /**
     * In some cases we want item info with the file.
     *
     * @todo check if we should use unilateral Doctrine for this instead.
     */
    private Item $item;

    /**
     * Holds the file data.
     * For now not using it.
      nullable: true
     */
    private stdClass $data;

    public function __construct(BaseFile $file)
    {
        $this->setFile($file);
        //$this->data = $fileInfo->getContents();
    }

    #[ORM\PostLoad]
    public function doPostLoad()
    {
        $this->source = "{$this->path}/{$this->fileName}";
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
    public function setFile(BaseFile|UploadedFile $file): void
    {
        $this->fileName = $file->getFilename();
        $this->mimeType = $file->getMimeType();
        $this->file = $file;
        $this->path = str_replace('\\', '/', $file->getPath()); // Always use / for directory separator
        $this->source = "{$this->path}/{$this->fileName}";
        $this->lastModified = $file->getMTime();
    }

    /**
     * Function to fix legacy paths saved in database.
     */
    public function cleanPaths(): void
    {
        $this->path = str_replace('\\', '/', $this->path);
    }

    /**
     * In accordance with php pathinfo.
     */
    public function getFileName(): string
    {
        if (null === $this->file) {
            return pathinfo($this->source, PATHINFO_FILENAME);
        }

        return $this->file->getBasename('.'.$this->file->getExtension());
    }

    /**
     * In accordance with php pathinfo.
     */
    public function getBaseName(): string
    {
        return $this->fileName;
    }

    public function getFile(): BaseFile
    {
        if (null === $this->file) {
            $this->file = new BaseFile();
        }

        return $this->file;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSource(): string
    {
        return $this->source;
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

    /**
     * Note: should not be used but required for Vich Uploader.
     * Use setFile instead.
     *
     * @todo keep an eye on Vich updates because this should not be happening.
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * Note: should not be used but required for Vich Uploader.
     * Use setFile instead.
     *
     * @todo keep an eye on Vich updates because this should not be happening.
     */
    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }
}
