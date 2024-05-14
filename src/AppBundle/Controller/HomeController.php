<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if($this->isGranted('ROLE_PARENT')){
            return $this->redirectToRoute('espace_parent_index');
        }

        $db = $this->getDoctrine()->getConnection();
        $sit_annee_paie = $db->query('SELECT * FROM sf3_situation_annee_paiement ORDER BY annee ASC');
        $sit_annee_sexe = $db->query('SELECT * FROM sf3_situation_annee_sexe ORDER BY annee ASC');
        $sit_annee_paie = $sit_annee_paie->fetchAll();
        $sit_annee_sexe = $sit_annee_sexe->fetchAll();

        //var_dump($sit_annee_paie);

        return $this->render('home/home_index.html.twig', [
            'sit_annee_paie' => $sit_annee_paie,
            'sit_annee_sexe' => $sit_annee_sexe,
        ]);
    }

}
