<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Depense;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Paiement;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

class ListeDepenseDateController extends Controller {

    /**
     * @Route("/etat/liste_depenses_entre_deux_dates", name="lst_dep_date")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $debut = new \DateTime(date('01-m-Y'));
        $fin = new \DateTime();

        $form = $this->createFormBuilder()
                ->add(
                        'debut', DateType::class, [
                    'widget' => 'single_text',
                    'data' => $debut,
                    'constraints' => [new NotBlank()],
                        ]
                )
                ->add(
                        'fin', DateType::class, [
                    'widget' => 'single_text',
                    'data' => $fin,
                    'constraints' => [new NotBlank()],
                        ]
                )
                ->add(
                        'type', ChoiceType::class, [
                    'choices' => Depense::getTypesDepense(),
                    'choice_label' => function ($choice) {
                        return $choice;
                    },
                    'placeholder' => '',
                    'required' => false,
                    'attr' => [
                        'class' => 'chosen-select',
                        'data-placeholder' => MyConfig::CHOSEN_TEXT,
                    ],
                        ]
                )
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $debut = $form->getData()['debut'];
            $fin = $form->getData()['fin'];
            $type = $form->getData()['type'];
            $criteres = '';
            $param = ['debut' => $debut, 'fin' => $fin];
            if ($type) {
                $criteres = 'and d.type = :type';
                $param['type'] = $type;
            }
            $sql = "select d from AppBundle\Entity\Depense d where d.date between :debut and :fin {$criteres} order by d.date desc";
            $query = $em->createQuery($sql);
            $query->setParameters($param);
            $depenses = $query->getResult();
            $total = 0;
            foreach ($depenses as $dep) {
                $total += $dep->getMontant();
            }

            $vue = $this->renderView(
                    'etat/liste_depense_date.html.twig', [
                'titre' => 'Liste des dépenses',
                'depenses' => $depenses,
                'total' => $total,
                'debut' => $debut,
                'fin' => $fin,
                'type' => $type,
                'asset' => MyConfig::asset(),
                    ]
            );

            return new Response($vue);

            /*
              $file    = 'liste_depenses_par_date_'.date('Y_m_d_His').'.pdf';
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
                        'etat/liste_depense_date_form.html.twig', [
                    'titre' => 'Etat - Liste des dépenses entre deux dates',
                    'form' => $form->createView(),
                        ]
        );
    }

}
