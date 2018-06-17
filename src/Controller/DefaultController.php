<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractSmartController
{
    /**
     * Matches /.
     *
     * @Route("/", name="intro", defaults={"_format": "html"})
     */
    public function intro()
    {
        return $this->smartRender('default/intro.html.twig');
    }

    /**
     * Matches /story/xxxx.
     *
     * @Route("/story/{title}", name="story", defaults={"_format": "html"})
     */
    public function viewStory()
    {
        return $this->smartRender('default/story.html.twig');
    }
}
