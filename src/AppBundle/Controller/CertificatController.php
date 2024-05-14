<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\Note;
use AppBundle\Entity\Parametres;
use AppBundle\Entity\Parents;
use AppBundle\Entity\Periode;
use AppBundle\Entity\Semestre;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/certificat")
 */
class CertificatController extends Controller
{
    /**
     * @Route("/fiche/{id}", name="certificat_show", requirements={"id":"\d+"})
     */
    public function showAction(Request $request, EtudiantClasse $etudiantClasse)
    {
        $em = $this->getDoctrine()->getManager();
        $signateur = $em->getRepository(Parametres::class)->findOneBy(['cle' => 'signateur_certificat_frequentation']);

        return $this->render('certificat/certificat_show.html.twig', [
            'etudiant_classe' => $etudiantClasse,
            'signateur' => $signateur,
        ]);
    }

}
