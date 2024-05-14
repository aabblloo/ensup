<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AutreDocument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Autredocument controller.
 *
 * @Route("autredocument")
 */
class AutreDocumentController extends Controller
{
    /**
     * Lists all autreDocument entities.
     *
     * @Route("/liste", name="autre_doc_index", methods="GET")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('debut', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control-sm'],
            ])
            ->add('fin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control-sm'],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => AutreDocument::getTypes(),
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'required' => false,
                'placeholder' => '',
                'attr' => ['class' => 'chosen-select'],
            ])
            ->getForm();
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $documents = $em->getRepository('AppBundle:AutreDocument')->findBy([], ['titre' => 'asc']);

        if ($form->isSubmitted() && $form->isValid()) {
            $critere = $form->getData();
            //var_dump($critere);exit;
            $query = $em->createQueryBuilder();
            $query->select('d')
                ->from(AutreDocument::class, 'd')
                ->where('d.id is not null');

            if ($critere['type']) {
                $query->andWhere('d.type = :type');
                $query->setParameter('type', $critere['type']);
            }

            if ($critere['debut']) {
                $query->andWhere('d.date >= :debut');
                $query->setParameter('debut', $critere['debut']);
            }

            if ($critere['fin']) {
                $query->andWhere('d.date <= :fin');
                $query->setParameter('fin', $critere['fin']);
            }

            $documents = $query->getQuery()->getResult();
        }

        return $this->render('autredocument/autre_doc_index.html.twig', array(
            'documents' => $documents,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new autreDocument entity.
     *
     * @Route("/ajouter", name="autre_doc_new", methods="GET|POST")
     */
    public function newAction(Request $request)
    {
        $autreDocument = new Autredocument();
        $form = $this->createForm('AppBundle\Form\AutreDocumentType', $autreDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $autreDocument->upload();
            $em->persist($autreDocument);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Autre Document Id:{$autreDocument->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Le document <b>{$autreDocument->getTitre()}</b> a été crée avec succès.");

            return $this->redirectToRoute('autre_doc_index');
        }

        return $this->render('autredocument/autre_doc_form.html.twig', array(
            'titre' => 'Enregistrer un document',
            'autreDocument' => $autreDocument,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a autreDocument entity.
     *
     * @Route("/{id}", name="autre_doc_show", methods="GET")
     */
    public function showAction(AutreDocument $autreDocument)
    {
        $deleteForm = $this->createDeleteForm($autreDocument);

        return $this->render('autredocument/autre_doc_show.html.twig', array(
            'autreDocument' => $autreDocument,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing autreDocument entity.
     *
     * @Route("/modifier/{id}", name="autre_doc_edit", methods="GET|POST")
     */
    public function editAction(Request $request, AutreDocument $autreDocument)
    {
        $form = $this->createForm('AppBundle\Form\AutreDocumentType', $autreDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $autreDocument->upload();
            $logs = new Logs($this->getUser(), 'Update', "Autre Document Id:{$autreDocument->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Le document <b>{$autreDocument->getTitre()}</b> a été modifié avec succès.");

            return $this->redirectToRoute('autre_doc_index');
        }

        return $this->render('autredocument/autre_doc_form.html.twig', array(
            'titre' => 'Modifier le document',
            'autreDocument' => $autreDocument,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a autreDocument entity.
     *
     * @Route("/supprimer/{id}", name="autre_doc_delete", methods="GET|DELETE")
     */
    public function deleteAction(Request $request, AutreDocument $autreDocument)
    {
        $form = $this->createDeleteForm($autreDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($autreDocument);
            unlink(\realpath('documents/' . $autreDocument->getLien()));
            $logs = new Logs($this->getUser(), 'Delete', "Autre Document Id:{$autreDocument->getId()}");
            $em->persist($logs);
            $this->addFlash('success',
                "Le document <b>{$autreDocument->getTitre()}</b> a été supprimé avec succès.");

            $em->flush();

            return $this->redirectToRoute('autre_doc_index');
        }

        return $this->render('autredocument/autre_doc_delete.html.twig', array(
            'autreDocument' => $autreDocument,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to delete a autreDocument entity.
     *
     * @param AutreDocument $autreDocument The autreDocument entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AutreDocument $autreDocument)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('autre_doc_delete', array('id' => $autreDocument->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
