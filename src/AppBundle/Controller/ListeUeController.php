<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ListeUe;
use AppBundle\Entity\Logs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Listeue controller.
 *
 * @Route("listeUe")
 */
class ListeUeController extends Controller
{
    /**
     * Lists all listeUe entities.
     *
     * @Route("/", name="listeUe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $listeUes = $em->getRepository('AppBundle:ListeUe')->findBy([], ['nom' => 'asc']);

        return $this->render('listeue/liste_ue_index.html.twig', array(
            'listeUes' => $listeUes,
        ));
    }

    /**
     * Creates a new listeUe entity.
     *
     * @Route("/ajouter", name="listeUe_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $listeUe = new Listeue();
        $form = $this->createForm('AppBundle\Form\ListeUeType', $listeUe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($listeUe);
            $logs = new Logs($this->getUser(), 'Insert', "Liste UE Id:{$listeUe->getId()}");
            $em->persist($logs);
            $em->flush();

            $this->addFlash('success',
                "UE <b>{$listeUe->getNom()}</b> enregistrée avec succès."
            );

            return $this->redirectToRoute('listeUe_index');
        }

        return $this->render('listeue/liste_ue_form.html.twig', array(
            'titre' => 'Ajouter une UE',
            'listeUe' => $listeUe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a listeUe entity.
     *
     * @Route("/fiche/{id}", name="listeUe_show")
     * @Method("GET")
     */
    public function showAction(ListeUe $listeUe)
    {
        $deleteForm = $this->createDeleteForm($listeUe);

        return $this->render('liste_ue_show.html.twig', array(
            'listeUe' => $listeUe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a listeUe entity.
     *
     * @param ListeUe $listeUe The listeUe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ListeUe $listeUe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('listeUe_delete', array('id' => $listeUe->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing listeUe entity.
     *
     * @Route("/modifier/{id}", name="listeUe_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ListeUe $listeUe)
    {
        // $deleteForm = $this->createDeleteForm($listeUe);/**/
        $editForm = $this->createForm('AppBundle\Form\ListeUeType', $listeUe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Liste UE Id:{$listeUe->getId()}");
            $em->persist($logs);
            $em->flush();

            $this->addFlash('success',
                "UE <b>{$listeUe->getNom()}</b> modifiée avec succès."
            );

            return $this->redirectToRoute('listeUe_index');
        }

        return $this->render('listeue/liste_ue_form.html.twig', array(
            'titre' => "Modifier l'UE",
            'listeUe' => $listeUe,
            'form' => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a section entity.
     *
     * @Route("/supprimer/{id}", name="listeUe_delete")
     * @Method("GET|POST")
     */
    public function deleteAction(Request $request, ListeUe $listeUe)
    {
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $listeUe->getId()],
                'constraints' => new EqualTo($listeUe->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($listeUe);
            $logs = new Logs($this->getUser(), 'Delete', "Liste UE Id:{$listeUe->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "UE <b>{$listeUe->getNom()}</b> supprimée de la liste avec succès."
            );

            return $this->redirectToRoute('listeUe_index');
        }

        return $this->render('listeue/liste_ue_delete.html.twig', [
            'titre' => "Supprimer l'UE de la liste",
            'listeUe' => $listeUe,
            'form' => $form->createView(),
        ]);
    }
}
