<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Matches /login on GET, HEAD.
     *
     * @Route("/login", name="login_page", methods={"GET","HEAD"}, schemes="https")
     */
    public function login(AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('default/security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
        ]);
    }

    /**
     * Matches /login on POST.
     *
     * @Route("/login", name="login_action", methods="POST", schemes="https")
     */
    public function doLogin(Request $request)
    {
    }

    /**
     * Matches /logout.
     *
     * @Route("/logout", name="logout", schemes="https")
     */
    public function doLogout(Request $request)
    {
        return $this->redirectToRoute('intro');
    }
}
