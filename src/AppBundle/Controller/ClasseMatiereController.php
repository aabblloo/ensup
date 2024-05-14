<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Classe;
use AppBundle\Entity\ClasseMatiere;
use AppBundle\Entity\Matiere;
use AppBundle\Form\ClasseMatiereType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Classematiere controller.
 *
 * @Route("classeMatiere")
 */
class ClasseMatiereController extends Controller
{

    /**
     * @Route("/", name="classe_matiere_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $classeMatieres = $em->getRepository('AppBundle:ClasseMatiere')->findAll();

        return $this->render('classematiere/parent_index.html.twig', array(
            'classeMatieres' => $classeMatieres,
        ));
    }

    /**
     * @Route("/ajouter/{id}", name="classe_matiere_new")
     */
    public function newAction(Request $request, Classe $classe)
    {
        $em = $this->getDoctrine()->getManager();
        $classeMatiere = new Classematiere();
        $classeMatiere->setClasse($classe);
        $form = $this->createForm('AppBundle\Form\ClasseMatiereType', $classeMatiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classeMatiere->upload();
            $em->persist($classeMatiere);
            $em->flush();
            $this->addFlash('success', "Le module <b>{$classeMatiere->getMatiere()->getCode()}</b> a été ajoutée avec succès.");
            return $this->redirectToRoute('classe_matiere_new', array('id' => $classe->getId()));
        }

        return $this->render('classe_matiere/classe_matiere_form.html.twig', array(
            'titre' => 'Enregistrer un module pour la classe',
            'classeMatiere' => $classeMatiere,
            'form' => $form->createView(),
            'matieres' => $em->getRepository(Matiere::class)->getMatieresByClasse($classe),
        ));
    }

    /**
     * @Route("/modifier/{id}", name="classe_matiere_edit")
     */
    public function editAction(Request $request, ClasseMatiere $classeMatiere)
    {
        $form = $this->createForm(ClasseMatiereType::class, $classeMatiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classeMatiere->upload();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Le module <b>{$classeMatiere->getMatiere()->getCode()}</b> a été modifiée avec succès.");
            return $this->redirectToRoute('classe_matiere_new', array('id' => $classeMatiere->getClasse()->getId()));
        }

        return $this->render('classe_matiere/classe_matiere_form.html.twig', array(
            'titre' => 'Modifier le module pour la classe',
            'classeMatiere' => $classeMatiere,
            'form' => $form->createView(),
            'edit' => true,
        ));
    }

    /**
     * @Route("/supprimer/{id}", name="classe_matiere_delete")
     */
    public function deleteAction(Request $request, ClasseMatiere $classeMatiere)
    {
        $form = $this->createForm(ClasseMatiereType::class, $classeMatiere);
        $form->remove('matiere');
        $form->remove('coeff');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($classeMatiere);
            $em->flush();
            $this->addFlash('success', "Le module <b>{$classeMatiere->getMatiere()->getCode()}</b> a été supptimée avec succès.");
            return $this->redirectToRoute('classe_matiere_new', ['id' => $classeMatiere->getClasse()->getId()]);
        }

        return $this->render('classe_matiere/classe_matiere_delete.html.twig', [
            'titre' => 'Suppression module',
            'classeMatiere' => $classeMatiere,
            'form' => $form->createView(),
        ]);
    }

}
