<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cycle;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Specialite;
use AppBundle\Form\CycleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/cycle")
 */
class CycleController extends Controller
{
    /**
     * @Route("/", name="cycle_index", methods="GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('departement_index');

        // return $this->render('cycle/cycle_index.html.twig', [
        //     'titre' => 'Liste des cycles',
        //     'cycles' => $this->getDoctrine()
        //         ->getRepository(Cycle::class)
        //         ->findBy([], ['ordre' => 'asc'])
        // ]);
    }

    /**
     * @Route("/ajouter/{id}", name="cycle_new", methods="GET|POST")
     */
    public function newAction(Request $request, Specialite $specialite)
    {
        return $this->redirectToRoute('departement_index');

        // $cycle = new Cycle();
        // $cycle->setSpecialite($specialite);
        // $form = $this->createForm(CycleType::class, $cycle);
        // $form->handleRequest($request);
        //
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $em = $this->getDoctrine()->getManager();
        //     $em->persist($cycle);
        //     $em->flush();
        //     $logs = new Logs($this->getUser(), 'Insert', "Cycle Id:{$cycle->getId()}");
        //     $em->persist($logs);
        //     $em->flush();
        //     $this->addFlash('success', "Cycle <b>{$cycle->getNom()}</b> crée avec succès.");
        //     return $this->redirectToRoute('specialite_show', ['id' => $cycle->getSpecialite()->getId()]);
        // }
        //
        // return $this->render('cycle/cycle_form.html.twig', [
        //     'titre' => 'Ajouter un cycle',
        //     'cycle' => $cycle,
        //     'form' => $form->createView(),
        // ]);
    }

    /**
     * @Route("/fiche/{id}", name="cycle_show", methods="GET")
     */
    public function showAction(Cycle $cycle)
    {
        return $this->redirectToRoute('departement_index');

        // return $this->render('cycle/cycle_index.html.twig', [
        //     'titre' => 'Liste des cycles',
        //     'cycle' => $cycle
        // ]);
    }

    /**
     * @Route("/modifier/{id}", name="cycle_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Cycle $cycle)
    {
        return $this->redirectToRoute('departement_index');

        // $form = $this->createForm(CycleType::class, $cycle);
        // $form->handleRequest($request);
        //
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $em = $this->getDoctrine()->getManager();
        //     $logs = new Logs($this->getUser(), 'Update', "Cycle Id:{$cycle->getId()}");
        //     $em->persist($logs);
        //     $em->flush();
        //     $this->addFlash('success', "Cycle <b>{$cycle->getNom()}</b> modifié avec succès.");
        //     return $this->redirectToRoute('specialite_show', ['id' => $cycle->getSpecialite()->getId()]);
        // }
        //
        // return $this->render('cycle/cycle_form.html.twig', [
        //     'titre' => 'Modfier le cycle',
        //     'cycle' => $cycle,
        //     'form' => $form->createView(),
        // ]);
    }

    /**
     * @Route("/supprimer/{id}", name="cycle_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Cycle $cycle)
    {
        return $this->redirectToRoute('departement_index');

        // if (count($cycle->getSemestres())) {
        //     $this->addFlash('danger', "Impossible de supprimer le cycle <b>{$cycle->getNom()}</b> car il y a des éléments associés.");
        //     return $this->redirectToRoute('specialite_show', ['id' => $cycle->getSpecialite()->getId()]);
        // }
        //
        // $form = $this->createFormBuilder()
        //     ->add('id', HiddenType::class, [
        //         'attr' => ['data' => $cycle->getId()],
        //         'constraints' => new EqualTo($cycle->getId())
        //     ])
        //     ->getForm();
        // $form->handleRequest($request);
        //
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $em = $this->getDoctrine()->getManager();
        //     $em->remove($cycle);
        //     $logs = new Logs($this->getUser(), 'Delete', "Cycle Id:{$cycle->getId()}");
        //     $em->persist($logs);
        //     $em->flush();
        //     $this->addFlash('success', "Cycle <b>{$cycle->getNom()}</b> supprimé avec succès.");
        //     return $this->redirectToRoute('specialite_show', ['id' => $cycle->getSpecialite()->getId()]);
        // }
        //
        // return $this->render('cycle/cycle_delete.html.twig', [
        //     'titre' => 'Supprimer le cycle',
        //     'cycle' => $cycle,
        //     'form' => $form->createView(),
        // ]);
    }
}
