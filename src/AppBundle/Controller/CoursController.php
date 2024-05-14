<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Logs;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Professeur;
use AppBundle\Form\CoursType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Cour controller.
 * @Route("cours")
 */
class CoursController extends Controller
{

    /**
     * Lists all cour entities.
     * @Route("/", name="cours_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        //$debut = new \DateTime(date('01-m-Y'));
        $debut = new \DateTime();
        $fin = new \DateTime();
        $prof = null;

        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('debut', DateType::class, [
                'widget' => 'single_text',
                'data' => $debut,
                'constraints' => [new NotBlank()],
            ])
            ->add('fin', DateType::class, [
                'widget' => 'single_text',
                'data' => $fin,
                'constraints' => [new NotBlank()],
            ])
            ->add('prof', EntityType::class, [
                'class' => Professeur::class,
                'choice_value' => 'id',
                'choice_label' => 'prenomNom',
                'placeholder' => '',
                'required' => false,
                'attr' => [
                    'class' => 'chosen-select',
                    'data-placeholder' =>
                        MyConfig::CHOSEN_TEXT,
                ],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $debut = $form->getData()['debut'];
            $fin = $form->getData()['fin'];
            $prof = $form->getData()['prof'];
        }

        $em = $this->getDoctrine()->getManager();

        $cours = $em->getRepository('AppBundle:Cours')->findByPeriode($debut, $fin, $prof);
        // $cours = $em->getRepository('AppBundle:Cours')->findByPeriode($debut, $fin, $prof, $this->getUser());

        return $this->render('cours/cours_index.html.twig', [
            'titre' => 'Liste des cours',
            'cours' => $cours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new cour entity.
     * @Route("/ajouter", name="cours_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cours = new Cours();
        $cours->setUser($this->getUser());
        $annee = $em->getRepository(AnScolaire::class)->getAnneeEnCours();
        $cours->setAnScolaire($annee);
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nbreHeure = $cours->getFin()->diff($cours->getDebut());
            $cours->setNbreHeure($nbreHeure->h);
            // $cours->setTaux($cours->getClasse()->getTaux());
            $em->persist($cours);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Cours Id: {$cours->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Cours <b>{$cours->getUe()->getNomComplet()}</b> "
                . "du <b>{$cours->getDate()->format('d-m-Y')}</b> "
                . "enregistré avec succès."
            );

            return $this->redirectToRoute('cours_new');
        }

        return $this->render('cours/cours_form.html.twig', [
            'titre' => 'Engeristrer un cours',
            'cours' => $cours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing cour entity.
     * @Route("/modifier/{id}", name="cours_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cours $cours)
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nbreHeure = $cours->getFin()->diff($cours->getDebut());
            $cours->setNbreHeure($nbreHeure->h);
            // $cours->setTaux($cours->getClasse()->getTaux());
            if (!$cours->getUser()) {
                $cours->setUser($this->getUser());
            }
            $logs = new Logs($this->getUser(), 'Update', "Cours Id: {$cours->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Cours <b>{$cours->getUe()->getNom()}</b> "
                . "du <b>{$cours->getDate()->format('d-m-Y')}</b> "
                . "modifié avec succès."
            );

            return $this->redirectToRoute('cours_index');
        }

        return $this->render('cours/cours_form.html.twig', [
            'titre' => 'Modifier le cours',
            'cour' => $cours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a cour entity.
     * @Route("/supprimer{id}", name="cours_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Cours $cours)
    {
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $cours->getId()],
                'constraints' => new EqualTo($cours->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cours);
            $logs = new Logs($this->getUser(), 'Delete', "Cours Id: {$cours->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Cours <b>{$cours->getUe()->getNomComplet()}</b> "
                . "du <b>{$cours->getDate()->format('d-m-Y')}</b> "
                . "supprimé avec succès."
            );

            return $this->redirectToRoute('cours_index');
        }

        return $this->render('cours/cours_delete.html.twig', [
            'titre' => 'Supprimer le cours',
            'cours' => $cours,
            'form' => $form->createView(),
        ]);
    }

}
