<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Section;
use AppBundle\Entity\Specialite;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\DepartementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class ListeEtudiantDepartementController extends Controller
{

    /**
     * @Route("/etat/liste_etudiants_par_departement", name="lst_etd_departement")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('departement', DepartementType::class, [
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->add('anScolaire', AnneeType::class, [
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $departement = $form->getData()['departement'];
            $anScolaire = $form->getData()['anScolaire'];

            $i = 0;
            $j = 0;
            $k = 0;
            $tableau = [];
            $sections = $em->getRepository(Section::class)->findBy(['departement' => $departement], ['nom' => 'asc']);

            // foreach ($sections as $section) {
            //     $tableau[$i] = $section;
            //     $specialites = $em->getRepository(Specialite::class)->findBy(['section' => $section], ['nom' => 'asc']);
            //     foreach ($specialites as $specialite) {
            //         $tableau[$i][$j] = $specialite;
            //     }
            // }

            // $etudiants = $em->getRepository(Etudiant::class)->listeParFiliere(
            //     $departement, $anScolaire
            // );

            $vue = $this->renderView('etat/liste_etudiant_departement.html.twig', [
                'titre' => 'Liste des étudiants par département',
                // 'etudiants' => $etudiants,
                'departement' => $departement,
                'anScolaire' => $anScolaire,
                'asset' => MyConfig::asset(),
            ]);

            return new Response($vue);

            /*
              $file    = 'liste_etudiants_par_departement_'.date('Y_m_d_His').'.pdf';
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


        return $this->render('etat/liste_etudiant_departement_form.html.twig', [
            'titre' => 'Etat - Liste des étudiants par département',
            'form' => $form->createView(),
        ]);
    }

}
