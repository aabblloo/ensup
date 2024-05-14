<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Professeur;
use AppBundle\Entity\ProfMatiere;
use AppBundle\Form\ProfMatiereType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Profmatiere controller.
 * @Route("professeurUe")
 */
class ProfMatiereController extends Controller
{

    /**
     * Creates a new profMatiere entity.
     * @Route("/ajouter/{id}", name="prof_matiere_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Professeur $prof)
    {
        $em = $this->getDoctrine()->getManager();
        $profMatiere = new ProfMatiere();
        $profMatiere->setProf($prof);
        $form = $this->createForm(ProfMatiereType::class, $profMatiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profMatiere);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "ProfMatiere Id: {$profMatiere->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "UE <b>{$profMatiere->getUe()->getNom()}</b> enregistrée avec succès.");

            return $this->redirectToRoute('prof_matiere_new', ['id' => $prof->getId()]);
        }

        return $this->render('prof_matiere/prof_matiere_form.html.twig', [
            'titre' => 'Ajouter une UE',
            'profMatiere' => $profMatiere,
            'prof' => $prof,
            'profMatieres' => $em->getRepository(ProfMatiere::class)
                ->findBy(['prof' => $prof], ['matiere' => 'asc']),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing profMatiere entity.
     * @Route("/modifier/{id}", name="prof_matiere_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ProfMatiere $profMatiere)
    {
        $form = $this->createForm(
            'AppBundle\Form\ProfMatiereType',
            $profMatiere
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs(
                $this->getUser(),
                'Update',
                "ProfMatiere Id: {$profMatiere->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "UE <b>{$profMatiere->getUe()->getNom()}</b> modifiée avec succès."
            );

            return $this->redirectToRoute('prof_matiere_new', ['id' => $profMatiere->getProf()->getId()]);
        }

        return $this->render('prof_matiere/prof_matiere_form.html.twig', [
            'titre' => 'Modifier le module enseignée',
            'profMatiere' => $profMatiere,
            'prof' => $profMatiere->getProf(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a profMatiere entity.
     * @Route("/supprimer/{id}", name="prof_matiere_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, ProfMatiere $profMatiere)
    {
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $profMatiere->getId()],
                'constraints' => new EqualTo($profMatiere->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profMatiere);
            $logs = new Logs($this->getUser(), 'Delete', "ProfMatiere Id:{$profMatiere->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "UE <b>{$profMatiere->getUe()->getNom()}</b> supprimée avec succès."
            );

            return $this->redirectToRoute('prof_show', ['id' => $profMatiere->getProf()->getId()]);
        }

        return $this->render('prof_matiere/prof_matiere_delete.html.twig', [
            'titre' => "Supprimer l'UE",
            'profMatiere' => $profMatiere,
            'form' => $form->createView(),
        ]);
    }

}
