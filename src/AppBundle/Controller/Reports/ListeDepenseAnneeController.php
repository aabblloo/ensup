<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\MyConfig;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class ListeDepenseAnneeController extends Controller
{

    /**
     * @Route("/etat/liste_depenses_par_annee_scolaire", name="lst_dep_annee")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('anScolaire', EntityType::class, [
                'label' => 'Année scolaire',
                'class' => AnScolaire::class,
                'choice_value' => 'id',
                'choice_label' => 'nom',
                'placeholder' => '',
                'required' => true,
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT,
                ],
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $anScolaire = $form->getData()['anScolaire'];
            $param = ['anScolaire' => $anScolaire];
            $sql
                = 'select d from AppBundle\Entity\Depense d where d.anScolaire = :anScolaire order by d.date desc';
            $query = $em->createQuery($sql);
            $query->setParameters($param);
            $depenses = $query->getResult();
            $total = 0;
            foreach ($depenses as $dep) {
                $total += $dep->getMontant();
            }

            $vue = $this->renderView(
                'etat/liste_depense_annee.html.twig',
                [
                    'titre' => 'Liste des dépenses',
                    'depenses' => $depenses,
                    'anScolaire' => $anScolaire,
                    'total' => $total,
                    'asset' => MyConfig::asset(),
                ]
            );

            return new Response($vue);

            /*
            $file    = 'liste_depenses_par_annee_scolaire_'.date('Y_m_d_His')
                .'.pdf';
            $options = MyConfig::printOption();

            return new Response(
                $this->get('knp_snappy.pdf')
                    ->getOutputFromHtml($vue, $options, true), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => "inline; filename=\"{$file}\"",
                ]
            );
            //*/
        }

        return $this->render(
            'etat/liste_depense_annee_form.html.twig',
            [
                'titre' => 'Etat - Liste des dépenses par année scolaire',
                'form' => $form->createView(),
            ]
        );
    }
}
