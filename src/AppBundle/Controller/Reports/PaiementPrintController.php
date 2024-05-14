<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Paiement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaiementPrintController extends Controller
{

    /**
     * @Route("/etat/paiement/imprimer/{id}", name="paiement_print")
     */
    public function indexAction(Request $request, Paiement $paiement)
    {
        $vue = $this->renderView('etat/paiement_fiche_print.html.twig', [
            'titre' => 'Fiche paiement',
            'paiement' => $paiement,
            'asset' => MyConfig::asset(),
        ]);

        return new Response($vue);

        // $file = 'fiche_paiement_' . date('Y_m_d_His') . '.pdf';
        // $options = MyConfig::printOption();

        // return new Response(
        //     $this->get('knp_snappy.pdf')
        //         ->getOutputFromHtml($vue, $options, true), 200, [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => "inline; filename=\"{$file}\"",
        //     ]
        // );

    }

    /**
     * @Route("/etat/paiement/synthese/imprimer/{id}", name="paiement_synthese_print")
     */
    public function syntheseAction(Request $request, Paiement $paiement)
    {
        $vue = $this->renderView('etat/paiement_synthese_print.html.twig', [
            'titre' => 'Fiche paiement',
            'paiement' => $paiement,
            'asset' => MyConfig::asset(),
        ]);

        return new Response($vue);

        // $file = 'fiche_paiement_' . date('Y_m_d_His') . '.pdf';
        // $options = MyConfig::printOption();

        // return new Response(
        //     $this->get('knp_snappy.pdf')
        //         ->getOutputFromHtml($vue, $options, true), 200, [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => "inline; filename=\"{$file}\"",
        //     ]
        // );

    }
}
