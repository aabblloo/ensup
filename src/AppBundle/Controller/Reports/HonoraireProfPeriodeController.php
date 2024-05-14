<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\Cours;
use AppBundle\Entity\MyConfig;
use AppBundle\Utils\ProfAnneePeriode;
use AppBundle\Utils\ProfAnneePeriodeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HonoraireProfPeriodeController extends Controller {

    /**
     * @Route("/etat/honoraire_prof_par_periode", name="honoraire_prof_periode")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $search = new ProfAnneePeriode();
        $form   = $this->createForm(ProfAnneePeriodeType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($search->getProf()) {
                $cours = $em->getRepository(Cours::class)->getHonoraireProfPeriode($search);
                $total = 0;
                foreach ($cours as $cr) {
                    $total += $cr['montant'];
                }

                $vue = $this->renderView('etat/honoraire_prof_periode.html.twig', [
                    'search' => $search,
                    'cours'  => $cours,
                    'total'  => $total,
                    'asset'  => MyConfig::asset(),
                ]);

                return new Response($vue);
                /*
                  $file    = "honoraire_prof_periode_{$search->getProf()->getId()}_" . date('Y_m_d_His') . '.pdf';
                  $options = MyConfig::printOption();

                  return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($vue, $options, true), 200, [
                  'Content-Type'        => 'application/pdf',
                  'Content-Disposition' => "inline; filename=\"{$file}\"",
                  ]);
                  // */
            } else {
                $cours = $em->getRepository(Cours::class)->getHonoraireAllProfPeriode($search);
                $total = 0;
                foreach ($cours as $cr) {
                    $total += $cr['montant'];
                }

                $vue = $this->renderView('etat/honoraire_all_prof_periode.html.twig', [
                    'search' => $search,
                    'cours'  => $cours,
                    'total'  => $total,
                    'asset'  => MyConfig::asset(),
                ]);

                return new Response($vue);

                /*
                  $file = "honoraire_all_prof_periode_" . date('Y_m_d_His') . '.pdf';
                  $options = MyConfig::printOption();

                  return new Response(
                  $this->get('knp_snappy.pdf')
                  ->getOutputFromHtml($vue, $options, true), 200, [
                  'Content-Type' => 'application/pdf',
                  'Content-Disposition' => "inline; filename=\"{$file}\"",
                  ]
                  );
                  // */
            }
        }

        return $this->render('etat/honoraire_prof_periode_form.html.twig', [
                    'form' => $form->createView(),
        ]);
    }

}
