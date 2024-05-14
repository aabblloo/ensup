<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cycle;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Semestre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/semestre")
 */
class SemestreController extends Controller
{
    /**
     * @Route("/{id}", name="semestre_index", methods="GET")
     */
    public function index(Cycle $cycle)
    {
        return $this->render('semestre/semestre_index.html.twig', [
            'titre' => 'Liste des semestres',
            'cycle' => $cycle,
            'semestres' => $this->getDoctrine()->getRepository(Semestre::class)
                ->findBy(['cycle' => $cycle], ['ordre' => 'asc']),
        ]);
    }

    /**
     * @Route("/ajouter/{id}", name="semestre_new", methods="GET|POST")
     */
    public function newAction(Request $request, Cycle $cycle): Response
    {
        $semestre = new Semestre();
        $semestre->setCycle($cycle);
        $form = $this->createForm('AppBundle\Form\SemestreType', $semestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($semestre);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert',
                "Semestre Id:{$semestre->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Semestre <b>{$semestre->getNom()}</b> enregistré avec succès."
            );

            return $this->redirectToRoute('semestre_show', ['id'=>$semestre->getId()]);
        }

        return $this->render('semestre/semestre_form.html.twig', [
            'titre' => 'Enregistrer un semestre',
            'semestre' => $semestre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fiche/{id}", name="semestre_show", methods="GET")
     */
    public function show(Semestre $semestre): Response
    {
        return $this->render('semestre/semestre_show.html.twig', ['semestre' => $semestre]);
    }

    /**
     * @Route("/{id}/edit", name="semestre_edit", methods="GET|POST")
     */
    public function edit(Request $request, Semestre $semestre): Response
    {
        $form = $this->createForm('AppBundle\Form\SemestreType', $semestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs(
                $this->getUser(),
                'Update',
                "Semestre Id:{$semestre->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le semestre <b>{$semestre->getCode()}</b> a été modifié avec succès."
            );

            return $this->redirectToRoute('semestre_index');
        }

        return $this->render('semestre/semestre_form.html.twig', [
            'semestre' => $semestre,
            'titre' => 'Modifier le semestre',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="semestre_delete", methods="GET|POST")
     */
    public function delete(Request $request, Semestre $semestre): Response
    {
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $semestre->getId()],
                'constraints' => new EqualTo($semestre->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($semestre);
            $logs = new Logs(
                $this->getUser(),
                'Delete',
                "Semestre Id:{$semestre->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le semestre <b>{$semestre->getCode()}</b> a été supprimé avec succès."
            );

            return $this->redirectToRoute('semestre_index');
        }

        return $this->render('semestre/semestre_delete.html.twig', [
            'titre' => 'Supprimer le semestre',
            'semestre' => $semestre,
            'form' => $form->createView(),
        ]);
    }
}
