<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Depense;
use AppBundle\Entity\MyConfig;
use AppBundle\Form\DepenseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/depense")
 */
class DepenseController extends Controller
{

    /**
     * @Route("/", name="depense_index", methods="GET|POST")
     */
    public function indexAction(Request $request)
    {
        $debut = new \DateTime(date('01-m-Y'));
        $fin = new \DateTime();
        $type = null;

        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('debut', DateType::class, [
                'widget' => 'single_text', 'data' => $debut
            ])
            ->add('fin', DateType::class, [
                'widget' => 'single_text', 'data' => $fin
            ])
            ->add('type', ChoiceType::class, [
                'choices' => Depense::getTypesDepense(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'placeholder' => '',
                'required' => false,
                'attr' => ['class' => 'chosen-select',
                    'data-placeholder' => MyConfig::CHOSEN_TEXT],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $debut = $form->getData()['debut'];
            $fin = $form->getData()['fin'];
            $type = $form->getData()['type'];
        }

        $depenses = $this->getDoctrine()->getRepository(Depense::class)
            ->findByPeriode($debut, $fin, $type);
        $total = 0;
        foreach ($depenses as $dep) {
            $total += $dep->getMontant();
        }

        return $this->render('depense/depense_index.html.twig', [
            'depenses' => $depenses,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajouter", name="depense_new", methods="GET|POST")
     */
    public function newAction(Request $request)
    {
        $depense = new Depense();
        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($depense);
            $em->flush();
            $this->addFlash(
                'success',
                "La dépense <b>{$depense->getRef()}</b> du <b>{$depense->getDate()->format('d/m/Y')}</b> a été enregistrée avec succès."
            );

            return $this->redirectToRoute('depense_index');
        }

        return $this->render(
            'depense/depense_form.html.twig', [
                'titre' => 'Enregistrer une dépense',
                'depense' => $depense,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/modifier/{id}", name="depense_edit", methods="GET|POST")
     * @Security("has_role('ROLE_DIRECTEUR')")
     */
    public function editAction(Request $request, Depense $depense)
    {
        $this->denyAccessUnlessGranted('ROLE_DIRECTEUR', null, "Accès refusé");

        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                "La dépense <b>{$depense->getRef()}</b> du <b>{$depense->getDate()->format('d/m/Y')}</b> a été modifiée avec succès."
            );

            return $this->redirectToRoute('depense_index');
        }

        return $this->render(
            'depense/depense_form.html.twig', [
                'titre' => 'Modifier la dépense',
                'depense' => $depense,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/imprimer/{id}", name="depense_print", methods="GET")
     */
    public function printAction(Depense $depense)
    {
        return $this->render(
            'depense/depense_show.html.twig', ['depense' => $depense]
        );
    }

    /**
     * @Route("/supprimer/{id}", name="depense_delete", methods="GET|POST")
     * @Security("has_role('ROLE_DIRECTEUR')")
     */
    public function deleteAction(Request $request, Depense $depense)
    {
        $this->denyAccessUnlessGranted('ROLE_DIRECTEUR', null, "Accès refusé");

        $form = $this->createFormBuilder()
            ->add(
                'id', HiddenType::class, [
                    'attr' => ['data' => $depense->getId()],
                    'constraints' => new EqualTo($depense->getId()),
                ]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($depense);
            $em->flush();
            $this->addFlash(
                'success',
                "La dépense <b>{$depense->getRef()}</b> du <b>{$depense->getDate()->format('d/m/Y')}</b> a été supprimée avec succès."
            );

            return $this->redirectToRoute('depense_index');
        }

        return $this->render(
            'depense/depense_delete.html.twig', [
                'titre' => 'Supprimer la dépense',
                'depense' => $depense,
                'form' => $form->createView(),
            ]
        );
    }
}
