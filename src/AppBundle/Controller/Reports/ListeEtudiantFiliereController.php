<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Filiere;
use AppBundle\Entity\MyConfig;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class ListeEtudiantFiliereController extends Controller {

    /**
     * @Route("/etat/liste_etudiants_par_filiere", name="lst_etd_filiere")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
                ->add(
                        'filiere', EntityType::class, [
                    'label' => 'Filière',
                    'class' => Filiere::class,
                    'choice_value' => 'id',
                    'choice_label' => 'code',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select',
                        'data-placeholder' => MyConfig::CHOSEN_TEXT,
                    ],
                    'constraints' => [new NotBlank(), new Valid()],
                        ]
                )
                ->add(
                        'anScolaire', EntityType::class, [
                    'label' => 'Année scolaire',
                    'class' => AnScolaire::class,
                    'choice_value' => 'id',
                    'choice_label' => 'nom',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select',
                        'data-placeholder' => MyConfig::CHOSEN_TEXT,
                    ],
                    'constraints' => [new NotBlank(), new Valid()],
                        ]
                )
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filiere = $form->getData()['filiere'];
            $anScolaire = $form->getData()['anScolaire'];
            $etudiants = $em->getRepository(Etudiant::class)->listeParFiliere(
                    $filiere, $anScolaire
            );

            $vue = $this->renderView(
                    'etat/liste_etudiant_filiere.html.twig', [
                'titre' => 'Liste des étudiants par filière',
                'etudiants' => $etudiants,
                'filiere' => $filiere,
                'anScolaire' => $anScolaire,
                'asset' => MyConfig::asset(),
                    ]
            );

            return new Response($vue);

            /*
              $file    = 'liste_etudiants_par_filiere_'.date('Y_m_d_His').'.pdf';
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
                        'etat/liste_etudiant_filiere_form.html.twig', [
                    'titre' => 'Etat - Liste des étudiants par filière',
                    'form' => $form->createView(),
                        ]
        );
    }

}
