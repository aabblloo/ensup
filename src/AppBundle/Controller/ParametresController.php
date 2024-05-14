<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parametres;
use AppBundle\Form\ParametresType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use AppBundle\Entity\Logs;

/**
 * @Route("/parametres")
 * @IsGranted("ROLE_SCOLARITE")
 */
class ParametresController extends AbstractController
{
    /**
     * @Route("/index", name="parametres_index", methods={"GET"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $parametres = $em->getRepository(\AppBundle\Entity\Parametres::class)->findAll();
        return $this->render('parametres/index.html.twig', [
            'parametres' => $parametres,
        ]);
    }

    /**
     * @Route("/new", name="parametres_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function new(Request $request): Response
    {
        $parametre = new Parametres();
        $form = $this->createForm(ParametresType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parametre);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Paramètre Id: {$parametre->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Le Paramètre <b>{$parametre->getCle()}</b> a été enregistré avec succès."
            );

            return $this->redirectToRoute('parametres_index');
        }

        return $this->render('parametres/new.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="parametres_show", methods={"GET"})
//     */
//    public function show(Parametres $parametre): Response
//    {
//        return $this->render('parametres/liste_ue_show.html.twig', [
//            'parametre' => $parametre,
//        ]);
//    }

    /**
     * @Route("/edit/{id}", name="parametres_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Parametres $parametre): Response
    {
        $form = $this->createForm(ParametresType::class, $parametre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Paramètre Id: {$parametre->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Le Paramètre <b>{$parametre->getCle()}</b> a été modifié avec succès."
            );

            return $this->redirectToRoute('parametres_index');
        }

        return $this->render('parametres/edit.html.twig', [
            'parametre' => $parametre,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="parametres_delete", methods={"DELETE"})
//     */
//    public function delete(Request $request, Parametres $parametre): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$parametre->getId(), $request->request->get('_token'))) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($parametre);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('parametres_index');
//    }
}
