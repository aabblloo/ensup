<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\Professeur;
use AppBundle\Form\DossierType;
use AppBundle\Repository\DossierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/dossier")
 */
class DossierController extends Controller
{

    /**
     * @Route("/", name="dossier_index", methods="GET")
     */
    public function indexAction(DossierRepository $dossierRepository)
    {
        return $this->render(
            'dossier/user_index.html.twig',
            ['dossiers' => $dossierRepository->findAll()]
        );
    }

    /**
     * @Route("/ajouter/{type}/{id}", name="dossier_new", methods="GET|POST",
     *                                requirements={"id":"\d+","p":"e|p"})
     */
    public function newAction($id, $type, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dossier = new Dossier();
        $dossiers = null;

        if ($type == 'e') {
            $etudiant = $em->getRepository(Etudiant::class)->find($id);

            if (!$etudiant)
                $this->createNotFoundException("Etudiant Id:{$id} n'existe pas.");

            $dossier->setEtudiant($etudiant);
            $dossiers = $em->getRepository(Dossier::class)
                ->findBy(['etudiant' => $etudiant], ['nom' => 'asc']);
        } else {
            $prof = $em->getRepository(Professeur::class)->find($id);

            if (!$prof)
                $this->createNotFoundException("Professeur Id:{$id} n'existe pas.");

            $dossier->setProf($prof);
            $dossiers = $em->getRepository(Dossier::class)
                ->findBy(['prof' => $prof], ['nom' => 'asc']);
        }

        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossier->upload();
            $em->persist($dossier);
            $em->flush();
            $this->addFlash('success',
                "Le dossier <b>{$dossier->getNom()}</b> a été enregistré avec succès.");

            if ($type == 'e')
                return $this->redirectToRoute('etudiant_show', ['id' => $id]);
            else
                return $this->redirectToRoute('prof_show', ['id' => $id]);

        }

        $pers = ($type == 'e') ? $etudiant : $prof;

        return $this->render('dossier/dossier_form.html.twig', [
            'titre' => 'Ajouter un dossier',
            'dossier' => $dossier,
            'personne' => $pers,
            'dossiers' => $dossiers,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="dossier_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Dossier $dossier)
    {
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossier->upload();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                "Dossier <b>{$dossier->getNom()}</b> modifié avec succès.");

            if ($dossier->getEtudiant())
                return $this->redirectToRoute('etudiant_show', ['id' => $dossier->getEtudiant()->getId()]);
            else
                return $this->redirectToRoute('prof_show', ['id' => $dossier->getProf()->getId()]);        }

        $pers = ($dossier->getEtudiant()) ? $dossier->getEtudiant() : $dossier->getProf();

        return $this->render('dossier/dossier_form.html.twig', [
            'titre' => 'Modifier le dossier',
            'dossier' => $dossier,
            'personne' => $pers,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="dossier_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Dossier $dossier)
    {
        $form = $this->createFormBuilder()
            ->add(
                'id',
                HiddenType::class,
                [
                    'attr' => ['data' => $dossier->getId()],
                    'constraints' => new EqualTo($dossier->getId()),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        $pers = ($dossier->getEtudiant()) ? $dossier->getEtudiant()
            : $dossier->getProf();

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dossier);
            $em->flush();
            $this->addFlash(
                'success',
                "Le dossier du <b>{$dossier->getNom()}</b> a été supprimé avec succès."
            );
            $url = ($dossier->getEtudiant()) ? $this->generateUrl(
                'etudiant_show',
                ['id' => $pers->getId()]
            ) : $this->generateUrl('prof_show', ['id' => $pers->getId()]);

            return $this->redirect($url);
        }


        return $this->render(
            'dossier/dossier_delete.html.twig',
            [
                'titre' => 'Supprimer le dossier',
                'dossier' => $dossier,
                'personne' => $pers,
                'form' => $form->createView(),
            ]
        );
    }
}
