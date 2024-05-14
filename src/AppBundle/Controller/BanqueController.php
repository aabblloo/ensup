<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Banque;
use AppBundle\Entity\Paiement;
use AppBundle\Form\BanqueType;
use AppBundle\Repository\BanqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @Route("/banque")
 */
class BanqueController extends Controller
{

    /**
     * @Route("/", name="banque_index", methods="GET")
     */
    public function indexAction()
    {
        return $this->render(
            'banque/banque_index.html.twig', [
                'titre'   => 'Liste des banques',
                'banques' => $this->getDoctrine()
                    ->getRepository(Banque::class)
                    ->findBy([], ['nom' => 'asc']),
            ]
        );
    }

    /**
     * @Route("/ajouter", name="banque_new", methods="GET|POST")
     */
    public function newAction(Request $request)
    {
        $banque = new Banque();
        $form   = $this->createForm(BanqueType::class, $banque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($banque);
            $em->flush();
            $logs = new Logs(
                $this->getUser(), 'Insert', "Banque Id:{$banque->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "La banque <b>{$banque->getNom()}</b> a été enregistrée avec succès."
            );

            return $this->redirectToRoute('banque_index');
        }

        return $this->render(
            'banque/banque_form.html.twig', [
                'titre'  => 'Enregistrer une banque',
                'banque' => $banque,
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/modifier/{id}", name="banque_edit", methods="GET|POST")
     */
    public function editAction(Banque $banque, Request $request)
    {
        $form = $this->createForm(BanqueType::class, $banque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em   = $this->getDoctrine()->getManager();
            $logs = new Logs(
                $this->getUser(), 'Update', "Banque Id:{$banque->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "La banque <b>{$banque->getNom()}</b> a été modifiée avec succès."
            );

            return $this->redirectToRoute('banque_index');
        }

        return $this->render(
            'banque/banque_form.html.twig', [
                'titre'  => 'Modifier la banque',
                'banque' => $banque,
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/supprimer/{id}", name="banque_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Banque $banque)
    {
        $form = $this->createFormBuilder()
            ->add(
                'id', HiddenType::class, [
                    'attr'        => ['data' => $banque->getId()],
                    'constraints' => new EqualTo($banque->getId()),
                ]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em        = $this->getDoctrine()->getManager();
            $paiements = $em->getRepository(Paiement::class)->findBy(
                ['banque' => $banque]
            );
            if ($paiements) {
                $this->addFlash(
                    'danger',
                    "Impossible de supprimer <b>{$banque->getNom()}</b> car il y a des paiements associés."
                );

                return $this->redirectToRoute(
                    'banque_delete', ['id' => $banque->getId()]
                );
            }

            $logs = new Logs(
                $this->getUser(), 'Delete', "Banque Id:{$banque->getId()}"
            );
            $em->persist($logs);
            $em->remove($banque);
            $em->flush();
            $this->addFlash(
                'success',
                "La banque <b>{$banque->getNom()}</b> a été supprimée avec succès."
            );

            return $this->redirectToRoute('banque_index');
        }

        return $this->render(
            'banque/banque_delete.html.twig', [
                'titre'  => 'Supprimer la banque',
                'banque' => $banque,
                'form'   => $form->createView(),
            ]
        );
    }
}
