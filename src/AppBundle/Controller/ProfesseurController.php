<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Professeur;
use AppBundle\Entity\ProfMatiere;
use AppBundle\Form\ProfesseurType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/professeur")
 */
class ProfesseurController extends Controller
{

    /**
     * @Route("/", name="prof_index", methods="GET")
     */
    public function indexAction()
    {
        return $this->render('professeur/prof_index.html.twig', [
            'titre' => 'Liste des professeurs',
            'professeurs' => $this->getDoctrine()->getRepository(Professeur::class)
                ->findBy([], ['prenom' => 'ASC', 'nom' => 'ASC']),
        ]);
    }

    /**
     * @Route("/ajouter", name="prof_new", methods="GET|POST")
     */
    public function newAction(Request $request)
    {
        $professeur = new Professeur();
        $form = $this->createForm(ProfesseurType::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($professeur);
            $em->flush();
            $this->addFlash(
                'success',
                "Le professeur <b>{$professeur->getPrenomNom()}</b> a été crée avec succès."
            );

            return $this->redirectToRoute('prof_index');
        }

        return $this->render(
            'professeur/prof_form.html.twig',
            [
                'titre' => 'Ajouter un professeur',
                'professeur' => $professeur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/fiche/{id}", name="prof_show", methods="GET")
     */
    public function showAction(Professeur $professeur)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render(
            'professeur/prof_show.html.twig',
            [
                'titre' => 'Fiche professeur',
                'prof' => $professeur,
                'profMatieres' => $em->getRepository(ProfMatiere::class)
                    ->findBy(['prof' => $professeur], ['matiere' => 'asc']),
                'dossiers' => $em->getRepository(Dossier::class)
                    ->findBy(['prof' => $professeur], ['nom' => 'ASC']),
            ]
        );
    }

    /**
     * @Route("/modifier/{id}", name="prof_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Professeur $professeur)
    {
        $form = $this->createForm(ProfesseurType::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                "Le professeur <b>{$professeur->getPrenomNom()}</b> a été modifié avec succès."
            );

            return $this->redirectToRoute('prof_index');
        }

        return $this->render(
            'professeur/prof_form.html.twig',
            [
                'titre' => 'Modifier le professeur',
                'professeur' => $professeur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/supprimer/{id}", name="prof_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Professeur $professeur)
    {
        $form = $this->createFormBuilder()
            ->add(
                'id',
                HiddenType::class,
                [
                    'attr' => ['data' => $professeur->getId()],
                    'constraints' => new EqualTo($professeur->getId()),
                ]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $profMatieres = $em->getRepository(ProfMatiere::class)->findBy(
                ['prof' => $professeur]
            );
            if ($profMatieres) {
                $this->addFlash(
                    'success',
                    "Impossible de supprimer le professeur <b>{$professeur->getPrenomNom()}</b> car il y a des modules associées."
                );

                return $this->redirectToRoute('prof_index');
            }
            $em->remove($professeur);
            $em->flush();
            $this->addFlash(
                'success',
                "Le professeur <b>{$professeur->getPrenomNom()}</b> a été supprimé avec succès."
            );

            return $this->redirectToRoute('prof_index');
        }

        return $this->render(
            'professeur/prof_delete.html.twig',
            [
                'titre' => 'Supprimer le professeur',
                'professeur' => $professeur,
                'form' => $form->createView(),
            ]
        );
    }
}
