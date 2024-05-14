<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Section;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * Departement controller.
 *
 * @Route("departement")
 */
class DepartementController extends Controller
{
    /**
     * Lists all departement entities.
     *
     * @Route("/", name="departement_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $param = [];

        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER') {
            $param['id'] = $user->getDepartement()->getId();
        }

        $departements = $em->getRepository('AppBundle:Departement')
            ->findBy($param, ['nom' => 'asc']);

        return $this->render('departement/departement_index.html.twig', [
            'departements' => $departements,
        ]);
    }

    /**
     * Creates a new departement entity.
     *
     * @Route("/ajouter", name="departement_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $departement = new Departement();
        $form = $this->createForm('AppBundle\Form\DepartementType', $departement);
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $nbre = 1;
            $last = $em->getRepository(Departement::class)->findBy([], ['id' => 'desc'], 1);
            if (count($last)) {
                $nbre += $last[0]->getId();
            }

            $departement->setCode('DER' . $nbre);
            $em->persist($departement);

            $section = new Section();
            $section->setDepartement($departement);
            $section->setNom($departement->getNom());

            $nbre = 1;
            $last = $em->getRepository(Section::class)->findBy([], ['id' => 'desc'], 1);
            if (count($last)) {
                $nbre += $last[0]->getId();
            }

            $section->setCode('SEC' . $nbre);
            $em->persist($section);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Département Id:{$departement->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Département <b>{$departement->getNom()}</b> enregistré avec succès."
            );
            $this->addFlash('info',
                "Une section du même nom a été créée au cas où le département n'a pas de section, 
                    dans le cas contraire supprimez-le."
            );

            return $this->redirectToRoute('departement_show', ['id' => $departement->getId()]);
        }

        return $this->render('departement/departement_form.html.twig', [
            'titre' => 'Enregistrer un département',
            'departement' => $departement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a departement entity.
     *
     * @Route("/fiche/{id}", name="departement_show")
     * @Method("GET")
     */
    public function showAction(Departement $departement)
    {
        if (!$this->testUser($departement)) {
            return $this->redirectToRoute('departement_index');
        }

        $deleteForm = $this->createDeleteForm($departement);

        return $this->render('departement/departement_show.html.twig', array(
            'departement' => $departement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function testUser(Departement $departement)
    {
        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER' or $user->getRolePrincipal() == 'ROLE_SAISIE') {
            if ($user->getDepartement()->getId() != $departement->getId()) {
                $this->addFlash('warning', "Vous n'avez pas accès à cet département.");
                return false;            
            }
        }

        return true;
    }

    /**
     * Creates a form to delete a departement entity.
     *
     * @param Departement $departement The departement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Departement $departement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('departement_delete', array('id' => $departement->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to edit an existing departement entity.
     *
     * @Route("/modifier/{id}", name="departement_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Departement $departement)
    {
        if (!$this->testUser($departement)) {
            return $this->redirectToRoute('departement_index');
        }

        $form = $this->createForm('AppBundle\Form\DepartementType', $departement);
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($departement);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Update',
                "Departement Id:{$departement->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Département <b>{$departement->getNom()}</b> modifié avec succès."
            );

            return $this->redirectToRoute('departement_show', ['id' => $departement->getId()]);
        }

        return $this->render('departement/departement_form.html.twig', array(
            'departement' => $departement,
            'titre' => 'Modifier le département',
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a departement entity.
     *
     * @Route("/supprimer/{id}", name="departement_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Departement $departement)
    {
        if (!$this->testUser($departement)) {
            return $this->redirectToRoute('departement_index');
        }

        if ($departement->getSections() or $departement->getSpecialites()) {
            $this->addFlash('danger', 'Suppression impossible car il y a des éléments associés.');
            return $this->redirectToRoute('departement_show', ['id' => $departement->getId()]);
        }

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $departement->getId()],
                'constraints' => new EqualTo($departement->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($departement);
            $em->flush();
            $this->addFlash('success',
                "Département {$departement->getNom()} supprimé avec succès.");

            return $this->redirectToRoute('departement_index');
        }

        return $this->render('departement/departement_delete.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
        ]);
    }
}
