<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Cours;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Professeur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class HonoraireProfController extends Controller
{

    /**
     * @Route("/etat/honoraire_mensuel_prof", name="honoraire_prof")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('prof', EntityType::class, [
                'class' => Professeur::class,
                'choice_value' => 'id',
                'choice_label' => 'prenomNom',
                'placeholder' => '',
                'required' => false,
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT,
                ],
            ])
            ->add('anScolaire', EntityType::class, [
                'label' => 'Année scolaire',
                'class' => AnScolaire::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT,
                ],
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->add('mois', ChoiceType::class, [
                'choices' => Cours::getMois(),
                // 'choice_label' => function ($choice) {
                //     return $choice;
                // },
                'placeholder' => '',
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT,
                ],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prof = $form->getData()['prof'];
            $annee = $form->getData()['anScolaire'];
            $mois = $form->getData()['mois'];
            $em = $this->getDoctrine()->getManager();

            if ($prof) {
                $cours = $em->getRepository(Cours::class)
                    ->getHonoraireProf($prof, $annee, $mois);
                $total = 0;
                foreach ($cours as $cr) {
                    $total += $cr['montant'];
                }

                $vue = $this->renderView('etat/honoraire_mensuel_prof.html.twig', [
                    'prof' => $prof,
                    'annee' => $annee,
                    'mois' => array_search($mois, Cours::getMois()),
                    'cours' => $cours,
                    'total' => $total,
                    'asset' => MyConfig::asset(),
                ]);

                return new Response($vue);

                /*
                  $file    = "honoraire_mensuel_prof_{$prof->getId()}_"
                  .date('Y_m_d_His').'.pdf';
                  $options = MyConfig::printOption();

                  return new Response(
                  $this->get('knp_snappy.pdf')
                  ->getOutputFromHtml($vue, $options, true), 200, [
                  'Content-Type'        => 'application/pdf',
                  'Content-Disposition' => "inline; filename=\"{$file}\"",
                  ]
                  );
                  // */
            } else {
                $cours = $em->getRepository(Cours::class)
                    ->getHonoraireAllProf($annee, $mois);
                $total = 0;
                foreach ($cours as $cr) {
                    $total += $cr['montant'];
                }

                $vue = $this->renderView(
                    'etat/honoraire_mensuel_all_prof.html.twig', [
                        'annee' => $annee,
                        'mois' => array_search($mois, Cours::getMois()),
                        'cours' => $cours,
                        'total' => $total,
                        'asset' => MyConfig::asset(),
                    ]
                );

                return new Response($vue);

                /*
                  $file = "honoraire_mensuel_all_prof_" . date('Y_m_d_His')
                  . '.pdf';
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

        return $this->render(
            'etat/honoraire_mensuel_prof_form.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }

}
