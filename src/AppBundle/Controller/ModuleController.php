<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Module controller.
 *
 * @Route("module-00")
 */
class ModuleController extends Controller
{
    /**
     * Lists all module entities.
     *
     * @Route("/", name="module_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render(
            'module/module_index.html.twig', [
                'titre'   => 'Liste des modules',
                'modules' => $this->getDoctrine()
                    ->getRepository(Module::class)
                    ->findBy([], ['code' => 'asc']),
            ]
        );
    }

    /**
     * Creates a new module entity.
     *
     * @Route("/ajouter", name="module_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $module = new Module();
        $form = $this->createForm('AppBundle\Form\ModuleType', $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($module);
            $em->flush();
            $logs = new Logs(
                $this->getUser(), 'Insert', "Module Id:{$module->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le module <b>{$module->getCode()}</b> a été enregistré avec succès."
            );

            return $this->redirectToRoute('module_index');
        }

        return $this->render('module/module_form.html.twig', [
            'titre'  => 'Enregistrer un module',
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a module entity.
     *
     * @Route("/{id}", name="module_show")
     * @Method("GET")
     */
    public function showAction(Module $module)
    {
        $deleteForm = $this->createDeleteForm($module);

        return $this->render('module/liste_ue_show.html.twig', array(
            'module' => $module,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing module entity.
     *
     * @Route("/{id}/edit", name="module_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Module $module)
    {
        $form = $this->createForm('AppBundle\Form\ModuleType', $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em   = $this->getDoctrine()->getManager();
            $logs = new Logs(
                $this->getUser(), 'Update', "Module Id:{$module->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le module <b>{$module->getCode()}</b> a été modifié avec succès."
            );

            return $this->redirectToRoute('module_index');
        }

        return $this->render('module/module_form.html.twig', [
            'module' => $module,
            'titre'  => 'Modifier la module',
            'form' => $form->createView(),
        ]); 
    }

    /**
     * Deletes a module entity.
     *
     * @Route("/supprimer/{id}", name="module_delete")
     * @Method("GET|POST")
     */
    public function deleteAction(Request $request, Module $module)
    {
        $form = $this->createFormBuilder()
            ->add(
                'id', HiddenType::class, [
                    'attr'        => ['data' => $module->getId()],
                    'constraints' => new EqualTo($module->getId()),
                ]
            )
            ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            $em->remove($module);  
            $logs = new Logs(
                $this->getUser(), 'Delete', "Module Id:{$module->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le module <b>{$module->getCode()}</b> a été supprimé avec succès."
            );

            return $this->redirectToRoute('module_index');
        }

        return $this->render(
            'module/module_delete.html.twig', [
                'titre'  => 'Supprimer le module',
                'module' => $module,
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a module entity.
     *
     * @param Module $module The module entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Module $module)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('module_delete', array('id' => $module->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
