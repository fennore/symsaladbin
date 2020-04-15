<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractSmartController
{
    /**
     * Matches /login on GET, HEAD.
     *
     * @Route("/login", name="login_page", methods={"GET","HEAD"})
     */
    public function login(AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->smartRender('default/security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
        ]);
    }

    /**
     * Matches /login on POST.
     *
     * @Route("/login", name="login_action", methods="POST")
     */
    public function doLogin(Request $request)
    {
        return null;
    }

    /**
     * Matches /logout.
     *
     * @Route("/logout", name="logout")
     */
    public function doLogout(Request $request)
    {
        return $this->redirectToRoute('intro');
    }
}
