<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityEtudiantController extends Controller
{

    /**
     * @Route("/espace/etudiant/login", name="login_espace_etudiant")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        //get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login_espace_etudiant.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
        ));
    }
    
    /**
     * @Route("/espace/etudiant/logout", name="logout_espace_etudiant")
     */
    public function logoutAction()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception("Don't forget to activate logout in security.yaml");
    }

}
