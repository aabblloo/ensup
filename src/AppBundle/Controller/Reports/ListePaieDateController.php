<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Paiement;
use AppBundle\Utils\EtudiantPeriode;
use AppBundle\Utils\EtudiantPeriodeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ListePaieDateController extends Controller
{

    /**
     * @Route("/etat/liste_paiements_entre_deux_dates", name="lst_paie_date")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $search = new EtudiantPeriode();
        $search->setDebut(new \DateTime(date('01-m-Y')));
        $search->setFin(new \DateTime());
        $form = $this->createForm(EtudiantPeriodeType::class, $search);
        $form->remove('etudiant');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paiements = $em->getRepository(Paiement::class)->findByPeriode($search);

            $total = 0;
            foreach ($paiements as $paie) {
                $total += $paie->getMontant();
            }

            $vue = $this->renderView(
                'etat/liste_paiement_date.html.twig', [
                    'titre' => 'Liste des paiements',
                    'paiments' => $paiements,
                    'total' => $total,
                    'debut' => $search->getDebut(),
                    'fin' => $search->getFin(),
                    'asset' => MyConfig::asset(),
                ]
            );

            return new Response($vue);

            /*
        $file    = 'liste_paiements_par_date_'.date('Y_m_d_His').'.pdf';
        $options = MyConfig::printOption();

        return new Response(
        $this->get('knp_snappy.pdf')
        ->getOutputFromHtml($vue, $options, true), 200, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"{$file}\"",
        ]
        );
        // */
        }

        return $this->render(
            'etat/liste_paiement_date_form.html.twig', [
                'titre' => 'Etat - Liste des paiements entre deux dates',
                'form' => $form->createView(),
            ]
        );
    }

}
