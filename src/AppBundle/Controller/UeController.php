<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Semestre;
use AppBundle\Entity\Ue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Ue controller.
 *
 * @Route("unite-d-enseignement")
 */
class UeController extends Controller
{
    /**
     * Lists all ue entities.
     *
     * @Route("/{id}", name="ue_index")
     * @Method("GET")
     */
    public function indexAction(Semestre $semestre)
    {
        return $this->redirectToRoute('departement_index');

        // return $this->render('ue/ue_index.html.twig', [
        //     'ues' => $this->getDoctrine()->getRepository(Ue::class)
        //         ->findBy(['semestre' => $semestre], ['non' => 'asc']),
        // ]);
    }

    /**
     * Creates a new ue entity.
     *
     * @Route("/ajouter/{id}", name="ue_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Classe $classe)
    {
        if (!$this->testUser($classe->getCycle()->getSpecialite()->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_index');
        }

        $ue = new Ue();
        $ue->setClasse($classe);
        $form = $this->createForm('AppBundle\Form\UeType', $ue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nbre = $em->getRepository(Ue::class)->findAll();
            $nbre = count($nbre) + 1;
            $ue->setCode('UE' . $nbre);
            $em->persist($ue);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert',
                "UE Id:{$ue->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "UE <b>{$ue->getNom()}</b> enregistrée avec succès.");

            return $this->redirectToRoute('classe_show', ['id' => $ue->getClasse()->getId()]);
        }

        return $this->render('ue/ue_form.html.twig', [
            'titre' => 'Enregistrer une UE',
            'ue' => $ue,
            'form' => $form->createView(),
        ]);
    }

    private function testUser(Departement $departement)
    {
        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER' and $user->getDepartement()->getId() != $departement->getId()) {
            $this->addFlash('warning', "Vous n'avez pas accès à cette unité d'enseignement.");
            return false;
        }

        return true;
    }

    /**
     * Finds and displays a ue entity.
     *
     * @Route("/fiche/{id}", name="ue_show")
     * @Method("GET")
     */
    public function showAction(Ue $ue)
    {
        if (!$this->testUser($ue->getClasse()->getCycle()->getSpecialite()->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_index');
        }

        $deleteForm = $this->createDeleteForm($ue);

        return $this->render('ue/liste_ue_show.html.twig', array(
            'ue' => $ue,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a ue entity.
     *
     * @param Ue $ue The ue entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ue $ue)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ue_delete', array('id' => $ue->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing ue entity.
     *
     * @Route("/modifier/{id}", name="ue_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Ue $ue)
    {
        if (!$this->testUser($ue->getClasse()->getCycle()->getSpecialite()->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_index');
        }

        $form = $this->createForm('AppBundle\Form\UeType', $ue);
        $form->remove('classe');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update',
                "Ue Id:{$ue->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "UE <b>{$ue->getNom()}</b> modifiée avec succès."
            );

            return $this->redirectToRoute('classe_show', ['id' => $ue->getClasse()->getId()]);
        }

        return $this->render('ue/ue_form.html.twig', [
            'ue' => $ue,
            'titre' => "Modifier l'UE",
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a ue entity.
     *
     * @Route("/supprimer/{id}", name="ue_delete")
     * @Method("GET|POST")
     */
    public function deleteAction(Request $request, Ue $ue)
    {
        if (!$this->testUser($ue->getClasse()->getCycle()->getSpecialite()->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_index');
        }

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $ue->getId()],
                'constraints' => new EqualTo($ue->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ue);
            $logs = new Logs($this->getUser(), 'Delete',
                "UE Id:{$ue->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "UE <b>{$ue->getNom()}</b> a été supprimée avec succès."
            );

            return $this->redirectToRoute('classe_show', ['id' => $ue->getClasse()->getId()]);
        }

        return $this->render('ue/ue_delete.html.twig', [
            'titre' => "Supprimer l'UE",
            'ue' => $ue,
            'form' => $form->createView(),
        ]);
    }

}
