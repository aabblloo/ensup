<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Entity\ChangePassword;
use AppBundle\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\Logs;

/**
 * @Route("utilisateur")
 */
class UserController extends Controller
{

    /**
     * @Route("/", name="user_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->liste();
        return $this->render('user/user_index.html.twig', [
            'titre' => 'Liste des utilisateurs',
            'users' => $users
        ]);
    }

    /**
     * @Route("/ajouter", name="user_new")
     */
    public function newAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "User Id:{$user->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Utilisateur <b>{$user->getUsername()}</b> ajouté avec succès.");
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/user_form.html.twig', [
            'titre' => 'Ajouter un utilisateur',
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="user_edit", requirements={"id"="\d+"})
     */
    public function editAction(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($user->getUsername() == 'super') {
            $msg = "Impossible de modifier le Super Administrateur";
            throw $this->createNotFoundException($msg);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPlainPassword()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }

            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "User Id:{$user->getId()}");
            $em->persist($user);
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Utilisateur <b>{$user->getUsername()}</b> a été modifié avec succès.");
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/user_form.html.twig', [
            'titre' => "Modifier l'utilisateur",
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="user_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(User $user, Request $request)
    {
        if ($user->getUsername() == 'super') {
            $msg = "Impossible de supprimer le Super Administrateur";
            throw $this->createNotFoundException($msg);
        }

        $form = $this->createFormBuilder(array())
            ->add('id', HiddenType::class, array(
                'attr' => ['value' => $user->getId()],
                'constraints' => new EqualTo($user->getId())
            ))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = $em->getRepository(Logs::class)->findBy(['user' => $user]);

            if ($this->getUser() == $user) {
                $this->addFlash('danger', "Impossible de supprimer l'utilisateur <b>{$user->getUsername()}</b> car il correspond à celui qui est connecté.");
                return $this->redirectToRoute('user_delete', ['id' => $user->getId()]);
            }

            if ($logs) {
                $this->addFlash('danger', "Impossible de supprimer l'utilisateur <b>{$user->getUsername()}</b> car il a des opérations associées.");
                return $this->redirectToRoute('user_delete', ['id' => $user->getId()]);
            }

            $em->remove($user);
            $logs = new Logs($this->getUser(), 'Delete', "User Id:{$user->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "L'Utilisateur <b>{$user->getUsername()}</b> supprimé avec succès.");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/user_delete.html.twig', [
            'titre' => "Supprimer l'utilisateur",
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/changerMotPasse", name="user_change_pass")
     */
    public function changePasswordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->setPasswordText($changePassword->getNewPassword());
            $password = $passwordEncoder->encodePassword($user, $changePassword->getNewPassword());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
            return $this->redirect($request->getUri());
//            return $this->redirect($this->generateUrl('user_change_pass'));
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
