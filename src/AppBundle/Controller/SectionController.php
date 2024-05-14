<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Section;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Section controller.
 *
 * @Route("section")
 */
class SectionController extends Controller
{
    /**
     * Lists all section entities.
     *
     * @Route("/{id}", name="section_index")
     * @Method("GET")
     */
    public function indexAction(Departement $departement)
    {
        return $this->redirectToRoute('departement_index');

        if (!$this->testUser($departement)) {
            $this->addFlash('warning', "Vous n'avez pas accès à cette section.");
            return $this->redirectToRoute('departement_show', ['id' => $departement->getId()]);
        }

        return $this->render('section/section_index.html.twig', [
            'titre' => 'Liste des sections',
            'departement' => $departement,
            'sections' => $this->getDoctrine()->getRepository(Section::class)
                ->findBy(['departement' => $departement], ['nom' => 'asc']),
        ]);
    }

    private function testUser(Departement $departement)
    {
        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER' or $user->getRolePrincipal() == 'ROLE_SAISIE') {
            if ($user->getDepartement()->getId() != $departement->getId()){
                $this->addFlash('warning', "Vous n'avez pas accès à cette section.");
                return false;
            }
        }

        return true;
    }

    /**
     * Creates a new section entity.
     *
     * @Route("/ajouter/{id}", name="section_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Departement $departement)
    {
        if (!$this->testUser($departement)) {
            return $this->redirectToRoute('departement_index');
        }

        $section = new Section();
        $section->setDepartement($departement);
        $form = $this->createForm('AppBundle\Form\SectionType', $section);
        $form->remove('departement');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nbre = $em->getRepository(Section::class)->findAll();
            $nbre = count($nbre) + 1;
            $section->setCode('SEC' . $nbre);
            $em->persist($section);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Section Id:{$section->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Section <b>{$section->getNom()}</b> enregistrée avec succès."
            );

            return $this->redirectToRoute('departement_show', ['id' => $departement->getId()]);
        }

        return $this->render('section/section_form.html.twig', [
            'titre' => 'Enregistrer une section',
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a section entity.
     *
     * @Route("/fiche/{id}", name="section_show")
     * @Method("GET")
     */
    public function showAction(Section $section)
    {
        if (!$this->testUser($section->getDepartement())) {
            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        $deleteForm = $this->createDeleteForm($section);

        return $this->render('section/section_show.html.twig', array(
            'section' => $section,
            'departement' => $section->getDepartement(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a section entity.
     *
     * @param Section $section The section entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Section $section)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('section_delete', array('id' => $section->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing section entity.
     *
     * @Route("/modifier/{id}", name="section_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Section $section)
    {
        if (!$this->testUser($section->getDepartement())) {
            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        $form = $this->createForm('AppBundle\Form\SectionType', $section);
        $form->remove('departement');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Section Id:{$section->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "La section <b>{$section->getNom()}</b> a été modifiée avec succès."
            );

            return $this->redirectToRoute('departement_show', ['id' => $section->getDepartement()->getId()]);
        }

        return $this->render('section/section_form.html.twig', [
            'section' => $section,
            'titre' => 'Modifier la section',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a section entity.
     *
     * @Route("/supprimer/{id}", name="section_delete")
     * @Method("GET|POST")
     */
    public function deleteAction(Request $request, Section $section)
    {
        if (!$this->testUser($section->getDepartement())) {
            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        if (count($section->getSpecialites())) {
            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $section->getId()],
                'constraints' => new EqualTo($section->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($section);
            $logs = new Logs($this->getUser(), 'Delete', "Section Id:{$section->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "La section <b>{$section->getCode()}</b> a été supprimée avec succès."
            );

            return $this->redirectToRoute('departement_show', ['id' => $section->getDepartement()->getId()]);
        }

        return $this->render('section/section_delete.html.twig', [
            'titre' => 'Supprimer la section',
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

}
