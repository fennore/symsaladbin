<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use Symfony\Component\Routing\Annotation\Route;

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
