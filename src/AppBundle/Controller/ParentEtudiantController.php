<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use AppBundle\Entity\Parents;
use AppBundle\Form\MyType\EtudiantType;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use AppBundle\Entity\Logs;

/**
 * @Route("parent/etudiant")
 */
class ParentEtudiantController extends AbstractController
{

    /**
     * @Route("/index/{id}", name="parent_etd_index")
     */
    public function indexAction(Request $request, Parents $parent)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder([])
                ->add('etudiant', EntityType::class, [
                    'class' => Etudiant::class,
                    'choice_value' => 'id',
                    'choice_label' => 'mlePrenomNom',
                    'placeholder' => '',
                    'attr' => ['class' => 'chosen-select', 'data-placeholder' =>
                        MyConfig::CHOSEN_TEXT],
                ])
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eid = $form->getData()['etudiant']->getId();
            $etudiant = $em->getRepository(Etudiant::class)->find($eid);

            if ($parent->getEtudiants()->contains($etudiant)) {
                $this->addFlash('danger', 'Cet étudiant est déjà associé au parent.');
                goto AFFICHER;
            }

            $etudiant->setParent($parent);
            $em->persist($etudiant);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Update', "Etudiant Id:{$etudiant->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', 'Etudiant associé au parent avec succès.');
            return $this->redirectToRoute('parent_etd_index', ['id' => $parent->getId()]);
        }

        AFFICHER:
        $etudiants = $em->getRepository(Etudiant::class)->findBy(
                ['parent' => $parent], ['prenom' => 'asc', 'nom' => 'asc']
        );

        return $this->render('parents_etudiant/parent_etudiant_index.html.twig', [
                    'parent' => $parent,
                    'etudiants' => $etudiants,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="parent_etd_delete", methods="DELETE")
     * @Security("is_granted('ROLE_DIRECTEUR')")
     */
    public function deleteAction(Request $request, Parents $parent)
    {
        if ($this->isCsrfTokenValid('delete' . $parent->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $eid = $request->request->get('eid');
            $etudiant = $em->getRepository(Etudiant::class)->find($eid);
            
            if ($etudiant) {
                $logs = new Logs($this->getUser(), 'Update', "Etudiant Id:{$etudiant->getId()}");
                $em->persist($logs);
                $etudiant->setParent(null);
                $em->flush();
                $this->addFlash('success',
                        "L'Etudiant <b>{$etudiant->getPrenomNom()}</b> a été dissocié du parent.");
            }
        }

        return $this->redirectToRoute('parent_etd_index', ['id' => $parent->getId()]);
    }

}
