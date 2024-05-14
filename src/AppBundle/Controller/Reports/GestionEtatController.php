<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\EtudiantClasse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class GestionEtatController extends Controller
{
    /**
     * @Route("/gestion_scolaire/etats", name="gs_etat")
     */
    public function indexAction()
    {
        return $this->render('etat/gestion_index.html.twig', [
            'titre' => 'Gestion scolaire - Etats'
        ]);
    }

}
