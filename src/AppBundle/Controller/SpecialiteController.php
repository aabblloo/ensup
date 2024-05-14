<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cycle;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Section;
use AppBundle\Entity\Specialite;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Specialite controller.
 *
 * @Route("specialite")
 */
class SpecialiteController extends Controller
{
    /**
     * Lists all specialite entities.
     *
     * @Route("/", name="specialite_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('departement_index');

        // return $this->render('specialite/specialite_index.html.twig', [
        //     'titre' => 'Liste des spécialités',
        //     'specialites' => $this->getDoctrine()->getRepository(Specialite::class)
        //         ->findBy(['departement' => $departement], ['nom' => 'asc']),
        // ]);
    }

    /**
     * Creates a new specialite entity.
     *
     * @Route("/ajouter/{id}", name="specialite_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Section $section)
    {
        if (!$this->testUser($section->getDepartement())) {
            return $this->redirectToRoute('departement_show', ['id' => $section->getDepartement()->getId()]);
        }

        $specialite = new Specialite();
        $specialite->setSection($section);
        $form = $this->createForm('AppBundle\Form\SpecialiteType', $specialite);
        $form->remove('section');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nbre = $em->getRepository(Specialite::class)->findAll();
            $nbre = count($nbre) + 1;
            $specialite->setCode('SPL' . $nbre);

            $em->persist($specialite);
            $cyles = ['LIC' => 'Licence', 'MAST' => 'Master', 'DR' => 'Doctorat'];

            foreach ($cyles as $key => $value) {
                $cycle = new Cycle();
                $cycle->setSpecialite($specialite);
                $cycle->setCode($key);
                $cycle->setNom($value);
                $em->persist($cycle);
            }

            $em->flush();

            $logs = new Logs($this->getUser(), 'Insert', "Specialite Id:{$specialite->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Spécialité <b>{$specialite->getNom()}</b> enregistrée avec succès."
            );

            return $this->redirectToRoute('specialite_show', ['id' => $specialite->getId()]);
        }

        return $this->render('specialite/specialite_form.html.twig', [
            'titre' => 'Enregistrer une spécialité',
            'specialite' => $specialite,
            'form' => $form->createView(),
        ]);
    }

    private function testUser(Departement $departement)
    {
        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER' or $user->getRolePrincipal() == 'ROLE_SAISIE') {
            if ($user->getDepartement()->getId() != $departement->getId()) {
                $this->addFlash('warning', "Vous n'avez pas accès à cette spécialité.");
                return false;
            }
        }

        return true;
    }

    /**
     * Finds and displays a specialite entity.
     *
     * @Route("/fiche/{id}", name="specialite_show")
     * @Method("GET")
     */
    public function showAction(Request $request, Specialite $specialite)
    {
        if (!$this->testUser($specialite->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_show', ['id' => $specialite->getDepartement()->getId()]);
        }

        $deleteForm = $this->createDeleteForm($specialite);
        $cycle_select = null;
        $cycle_id = $request->query->get('cycle_id');

        if ($cycle_id) {
            $em = $this->getDoctrine()->getManager();
            $cycle_select = $em->getRepository(Cycle::class)->find($cycle_id);
        }

        return $this->render('specialite/specialite_show.html.twig', array(
            'specialite' => $specialite,
            'cycle_select' => $cycle_select,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a specialite entity.
     *
     * @param Specialite $specialite The specialite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Specialite $specialite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('specialite_delete', array('id' => $specialite->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing specialite entity.
     *
     * @Route("/modifier/{id}", name="specialite_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Specialite $specialite)
    {
        if (!$this->testUser($specialite->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_index');
        }

        $form = $this->createForm('AppBundle\Form\SpecialiteType', $specialite);
        $form->remove('section');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Specialite Id:{$specialite->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Spécialité <b>{$specialite->getNom()}</b> modifiée avec succès."
            );

            return $this->redirectToRoute('specialite_show', ['id' => $specialite->getId()]);
        }

        return $this->render('specialite/specialite_form.html.twig', [
            'specialite' => $specialite,
            'titre' => 'Modifier la spécialité',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a specialite entity.
     *
     * @Route("/supprimer/{id}", name="specialite_delete")
     * @Method("GET|POST")
     */
    public function deleteAction(Request $request, Specialite $specialite)
    {
        if (!$this->testUser($specialite->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_show', ['id' => $specialite->getDepartement()->getId()]);
        }

        if (count($specialite->getCycles())) {
            $this->addFlash('danger', 'Suppression impossible il y a des éléménts asscociés.');
            return $this->redirectToRoute('section_show', ['id' => $specialite->getSection()->getId()]);
        }

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $specialite->getId()],
                'constraints' => new EqualTo($specialite->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($specialite);
            $logs = new Logs($this->getUser(),
                'Delete', "Specialite Id:{$specialite->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Spécialité <b>{$specialite->getCode()}</b> supprimée avec succès."
            );

            return $this->redirectToRoute('section_show', ['id' => $specialite->getSection()->getId()]);
        }

        return $this->render('specialite/specialite_delete.html.twig', [
            'specialite' => $specialite,
            'form' => $form->createView(),
        ]);
    }

}
