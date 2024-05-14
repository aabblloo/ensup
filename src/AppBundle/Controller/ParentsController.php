<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\Parents;
use AppBundle\Form\ParentsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\Logs;

/**
 * @Route("/parent")
 */
class ParentsController extends AbstractController
{

    /**
     * @Route("/index", name="parents_index", methods="GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $parents = $em->getRepository(Parents::class)->findBy([], ['prenom' => 'asc', 'nom' => 'asc']);
        return $this->render('parents/parent_index.html.twig', ['parents' => $parents]);
    }

    /**
     * @Route("/new", name="parents_new", methods="GET|POST")
     */
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $parent = new Parents();
        $form = $this->createForm(ParentsType::class, $parent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parent->generatePassword();
            $password = $encoder->encodePassword($parent, $parent->getPasswordText());
            $parent->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($parent);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Parent Id:{$parent->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                    "Le parent <b>{$parent->getPrenomNom()}</b> a été enregistré avec succès."
            );

            return $this->redirectToRoute('parents_index');
        }

        return $this->render('parents/parent_new.html.twig', [
                    'parent' => $parent,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="parents_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Parents $parent, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(ParentsType::class, $parent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$parent->getPasswordText()) {
                $parent->generatePassword();
                $password = $encoder->encodePassword($parent, $parent->getPasswordText());
                $parent->setPassword($password);
            }

            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Parent Id:{$parent->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                    "Le parent <b>{$parent->getPrenomNom()}</b> a été modifié avec succès."
            );
            return $this->redirectToRoute('parents_index');
        }

        return $this->render('parents/parent_edit.html.twig', [
                    'parent' => $parent,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="parents_delete", methods="DELETE")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction(Request $request, Parents $parent)
    {
        if ($this->isCsrfTokenValid('delete' . $parent->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $etudiants = $em->getRepository(Etudiant::class)->findByParent($parent);

            foreach ($etudiants as $etd) {
                $etd->setParent(null);
                $em->persist($etd);
            }

            $logs = new Logs($this->getUser(), 'Delete', "Parent Id:{$parent->getId()}");
            $em->persist($logs);
            $this->addFlash('success',
                    "Le parent <b>{$parent->getPrenomNom()}</b> a été supprimé avec succès."
            );

            $em->remove($parent);
            $em->flush();
        }

        return $this->redirectToRoute('parents_index');
    }

}
