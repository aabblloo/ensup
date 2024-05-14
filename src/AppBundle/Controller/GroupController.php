<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Group;
use AppBundle\Form\GroupType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("groupe")
 */
class GroupController extends Controller
{

    /**
     * @Route("/", name="group_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository(Group::class)->liste();
        return $this->render('group/group_index.html.twig', [
            'titre' => 'Liste des groupes',
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/ajouter", name="group_new")
     */
    public function newAction(Request $request)
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $group->setRole(strtoupper($group->getRole()));
            $em->persist($group);
            $em->flush();
            $this->addFlash('success', "Le groupe <b>{$group->getName()}</b> a été ajouté avec succès.");
            return $this->redirectToRoute('group_index');
        }
        return $this->render('group/group_form.html.twig', [
            'titre' => 'Ajouter un groupe',
            'group' => $group,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="group_edit")
     */
    public function editAction(Group $group, Request $request)
    {
        if ($group->getRole() == 'ROLE_SUPER_ADMIN') {
            $msg = "Impossible de modifier le groupe Super administrateur";
            //throw $this->createNotFoundException($msg);
            throw $this->createAccessDeniedException($msg);
        }

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            $this->addFlash('success', "Le groupe <b>{$group->getName()}</b> a été modifié avec succès.");
            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/group_form.html.twig', [
            'titre' => 'Modifier le groupe',
            'group' => $group,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="group_delete")
     */
    public function deleteAction(Group $group, Request $request)
    {
        if ($group->getRole() == 'ROLE_SUPER_ADMIN') {
            $msg = "Impossible de supprimer le groupe Super administrateur";
            //throw $this->createNotFoundException($msg);
            throw $this->createAccessDeniedException($msg);
        }

        if ($group->getRole()){
            $this->addFlash('danger',"Impossible de supprimer le groupe <b>{$group->getName()}</b> car il y a des utilisateurs associés.");
            return $this->redirectToRoute('group_index');
        }

        $form = $this->createFormBuilder(array())
            ->add('id', HiddenType::class, array(
                'attr' => array('value' => $group->getId()),
                'constraints' => new EqualTo($group->getId())
            ))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();
            $this->addFlash('success', "Le groupe <b>{$group->getName()}</b> a été supprimé avec succès.");
            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/group_supprimer.html.twig', [
            'group' => $group,
            'form' => $form->createView()
        ]);
    }

}
