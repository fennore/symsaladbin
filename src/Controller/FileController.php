<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FileRepository;
use App\Entity\File;

class FileController extends AbstractSmartController
{
    /**
     * Matches /img/xxxx.
     *
     * @Route("/img/{path}", name="intro", defaults={"_format": "html"})
     */
    public function intro(FileRepository $fileRepository, $path)
    {
        // Render file
    }
}
