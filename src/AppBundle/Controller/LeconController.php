<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Lecon;
use AppBundle\Entity\ClasseMatiere;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Lecon controller.
 *
 * @Route("lecon")
 */
class LeconController extends Controller
{
    /**
     * Lists all lecon entities.
     *
     * @Route("/{id}", name="lecon_index")
     * @Method("GET")
     */
    public function indexAction(ClasseMatiere $classeMatiere)
    {
        $em = $this->getDoctrine()->getManager();

        $lecons = $em->getRepository('AppBundle:Lecon')->findAll();

        return $this->render('lecon/lecon_index.html.twig', array(
            'classeMatiere' => $classeMatiere,
            'lecons' => $lecons,
        ));
    }

    /**
     * Creates a new lecon entity.
     *
     * @Route("/new/{id}", name="lecon_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, ClasseMatiere $classeMatiere)
    {
        $em = $this->getDoctrine()->getManager();
        $lecon = new Lecon();
        $lecon->setClasseMatiere($classeMatiere);
        $form = $this->createForm('AppBundle\Form\LeconType', $lecon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lecon->uploadFile();
            $lecon->uploadVideo();
            $em->persist($lecon);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Leçon Id:{$lecon->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "La leçon <b>{$lecon->getTitre()}</b> a été créée avec succès.");
            return $this->redirectToRoute('lecon_new', array('id' => $classeMatiere->getId()));
        }

        $lecons = $em->getRepository(Lecon::class)->findBy(array('classeMatiere' => $classeMatiere));

        return $this->render('lecon/lecon_new.html.twig', array(
            'classeMatiere' => $classeMatiere,
            'lecons' => $lecons,
            'lecon' => $lecon,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lecon entity.
     *
     * @Route("/show/{id}", name="lecon_show")
     * @Method("GET")
     */
    public function showAction(Lecon $lecon)
    {
        // $deleteForm = $this->createDeleteForm($lecon);

        $classeMatiere= $lecon->getClasseMatiere();

        if(!$lecon->getVideo()){
            $this->addFlash('success', "Il n'y a pas de vidéo pour cette leçon.");
            return $this->redirectToRoute('lecon_new', array('id' => $classeMatiere->getId()));
        }

        return $this->render('lecon/lecon_show.html.twig', array(
            'lecon' => $lecon,
            // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lecon entity.
     *
     * @Route("/edit/{id}", name="lecon_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Lecon $lecon)
    {
        $classeMatiere = $lecon->getClasseMatiere();
        // $deleteForm = $this->createDeleteForm($lecon);
        $form = $this->createForm('AppBundle\Form\LeconType', $lecon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $lecon->uploadFile();
            $lecon->uploadVideo();
            $logs = new Logs($this->getUser(), 'Update', "Leçon Id:{$lecon->getId()}");
            $em->persist($logs);
            $this->addFlash('success', "La leçon <b>{$lecon->getTitre()}</b> a été modifiée avec succès.");
            $em->flush();
            return $this->redirectToRoute('lecon_new', array('id' => $classeMatiere->getId()));
        }

        return $this->render('lecon/lecon_edit.html.twig', array(
            'classeMatiere' => $classeMatiere,
            'lecon' => $lecon,
            'form' => $form->createView(),
            // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lecon entity.
     *
     * @Route("/delete/{id}", name="lecon_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, Lecon $lecon)
    {
        $classeMatiere = $lecon->getClasseMatiere();
        $form = $this->createDeleteForm($lecon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lecon);
            $dossier = realpath('cours') . DIRECTORY_SEPARATOR;

            if (is_file($dossier . $lecon->getDocument())) {
                \unlink($dossier . $lecon->getDocument());
            }

            if (is_file($dossier . $lecon->getVideo())) {
                \unlink($dossier . $lecon->getVideo());
            }
            
            $logs = new Logs($this->getUser(), 'Delete', "Leçon Id:{$lecon->getId()}");
            $em->persist($logs);
            $this->addFlash('success', "La leçon <b>{$lecon->getTitre()}</b> a été supprimée avec succès.");
            $em->flush();
            return $this->redirectToRoute('lecon_new', array('id' => $classeMatiere->getId()));
        }

        return $this->render('lecon/lecon_delete.html.twig', array(
            'lecon' => $lecon,
            'classeMatiere' => $classeMatiere,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a form to delete a lecon entity.
     *
     * @param Lecon $lecon The lecon entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Lecon $lecon)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lecon_delete', array('id' => $lecon->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
