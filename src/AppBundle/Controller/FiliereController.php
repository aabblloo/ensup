<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Filiere;
use AppBundle\Entity\Logs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Filiere controller.
 * @Route("filiere")
 */
class FiliereController extends Controller {

    /**
     * Lists all filiere entities.
     * @Route("/", name="filiere_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $filieres = $em->getRepository('AppBundle:Filiere')->findBy(
                [], ['code' => 'asc']
        );

        return $this->render(
                        'filiere/filiere_index.html.twig', [
                    'filieres' => $filieres,
                        ]
        );
    }

    /**
     * Creates a new filiere entity.
     * @Route("/new", name="filiere_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $filiere = new Filiere();
        $form = $this->createForm('AppBundle\Form\FiliereType', $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($filiere);
            $em->flush();
            $logs = new Logs(
                    $this->getUser(), 'Insert', "Filière Id:{$filiere->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                    'success',
                    "La filière <b>{$filiere->getCode()}</b> a été enregistrée avec succès."
            );

            return $this->redirectToRoute('filiere_index');
        }

        return $this->render(
                        'filiere/filiere_form.html.twig', [
                    'titre' => 'Enregistrer une filière',
                    'filiere' => $filiere,
                    'form' => $form->createView(),
                        ]
        );
    }

    /**
     * Finds and displays a filiere entity.
     * @Route("/{id}", name="filiere_show")
     * @Method("GET")
     */
    public function showAction(Filiere $filiere) {
        $deleteForm = $this->createDeleteForm($filiere);

        return $this->render(
                        'filiere/parent_show.html.twig', [
                    'filiere' => $filiere,
                    'delete_form' => $deleteForm->createView(),
                        ]
        );
    }

    /**
     * Displays a form to edit an existing filiere entity.
     * @Route("/{id}/edit", name="filiere_edit", requirements={"id":"\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Filiere $filiere) {
        $form = $this->createForm('AppBundle\Form\FiliereType', $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs(
                    $this->getUser(), 'Update', "Filière Id:{$filiere->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                    'success',
                    "La filière <b>{$filiere->getCode()}</b> a été modifiée avec succès."
            );

            return $this->redirectToRoute('filiere_index');
        }

        return $this->render(
                        'filiere/filiere_form.html.twig', [
                    'titre' => 'Modifier la filière',
                    'filiere' => $filiere,
                    'form' => $form->createView(),
                        ]
        );
    }

    /**
     * Deletes a filiere entity.
     * @Route("/supprimer/{id}", name="filiere_delete", requirements={"id":"\d+"})
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Filiere $filiere) {
        $form = $this->createFormBuilder()
                ->add(
                        'id', HiddenType::class, [
                    'attr' => ['data' => $filiere->getId()],
                    'constraints' => new EqualTo($filiere->getId()),
                        ]
                )
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $classes = $em->getRepository(Classe::class)->findBy(
                    ['filiere' => $filiere]
            );

            if ($classes) {
                $this->addFlash(
                        'danger',
                        "Impossible de supprimer car il y a des classes associées."
                );
                return $this->redirectToRoute(
                                'filiere_delete', ['id' => $filiere->getId()]
                );
            }

            $em->remove($filiere);
            $logs = new Logs(
                    $this->getUser(), 'Delete', "Filière Id:{$filiere->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                    'success',
                    "La filière <b>{$filiere->getCode()}</b> a été supprimée avec succès."
            );

            return $this->redirectToRoute('filiere_index');
        }

        return $this->render(
                        'filiere/filiere_delete.html.twig', [
                    'filiere' => $filiere,
                    'form' => $form->createView(),
                        ]
        );
    }

    /**
     * Creates a form to delete a filiere entity.
     *
     * @param Filiere $filiere The filiere entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Filiere $filiere) {
        return $this->createFormBuilder()
                        ->setAction(
                                $this->generateUrl(
                                        'filiere_delete', ['id' => $filiere->getId()]
                                )
                        )
                        ->setMethod('DELETE')
                        ->getForm();
    }

}
