<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\Parents;
use AppBundle\Entity\EtudiantClasse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("admin")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class AdminController extends Controller
{

    const tttt = 'dff';
    const MSG = 'Attenttion ! Cette opération est risquée. '
    . 'Si vous êtes sûr de ce que vous faites, '
    . 'veuillez editer le fichier et décommenter cette partie.';

    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/etd_password")
     */
    public function etdPassword(UserPasswordEncoderInterface $encoder)
    {
        $this->addFlash('danger', self::MSG);
        return $this->redirectToRoute('admin_index');

        $em = $this->getDoctrine()->getManager();
        //        $etudiants = $em->getRepository(Etudiant::class)->findBy([]);
        //         $etudiants = $em->getRepository(Etudiant::class)->findBy(['password' => null],[],100);
        $etudiants = $em->getRepository(Etudiant::class)->findBy(['password' => null]);

        foreach ($etudiants as $etd) {
            $etd->generatePassword();
            $password = $encoder->encodePassword($etd, $etd->getPasswordText());
            $etd->setPassword($password);
        }

        $em->flush();
        $this->addFlash('success', 'Mot de passe crée avec succès.');

        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/parent_password")
     */
    public function parentPassword(UserPasswordEncoderInterface $encoder)
    {
        //        $this->addFlash('danger', self::MSG);
        //        return $this->redirectToRoute('admin_index');

        $em = $this->getDoctrine()->getManager();
        $parents = $em->getRepository(Parents::class)->findBy([]);

        foreach ($parents as $p) {
            $p->generatePassword();
            $password = $encoder->encodePassword($p, $p->getPasswordText());
            $p->setPassword($password);
        }

        $em->flush();
        $this->addFlash('success', 'Mot de passe crée avec succès.');

        return $this->render('admin/index.html.twig');
    }

}

