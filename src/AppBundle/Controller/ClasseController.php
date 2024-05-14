<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Classe;
use AppBundle\Entity\Cycle;
use AppBundle\Entity\Departement;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Ue;
use AppBundle\Form\ClasseType;
use AppBundle\Repository\UeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/niveau-d-etude")
 */
class ClasseController extends Controller
{

    /**
     * @Route("/", name="classe_index", methods="GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('departement_index');

        // return $this->render('classe/classe_index.html.twig', [
        //     'titre' => "Liste des niveaux d'étude",
        //     'classes' => $this->getDoctrine()->getRepository(Classe::class)
        //         ->findBy([], ['ordre' => 'asc']),
        // ]);
    }

    /**
     * @Route("/ajouter/{id}", name="classe_new", methods="GET|POST")
     */
    public function newAction(Request $request, Cycle $cycle)
    {
        if (!$this->testUser($cycle->getSpecialite()->getSection()->getDepartement())) {
            return $this->redirectToRoute('departement_index');
        }

        $classe = new Classe();
        $classe->setCycle($cycle);
        $form = $this->createForm(ClasseType::class, $classe);
        $form->remove('cycle');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nbre = 1;
            $last = $em->getRepository(Classe::class)->findBy([], ['id' => 'desc'], 1);
            if (count($last)) {
                $nbre += $last[0]->getId();
            }
            $classe->setCode('CL' . $nbre);
            $em->persist($classe);
            $logs = new Logs($this->getUser(), 'Insert', "Classe Id:{$classe->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Niveau d'étude <b>{$classe->getNom()}</b> crée avec succès.");
            return $this->redirectToRoute('specialite_show', [
                'id' => $classe->getCycle()->getSpecialite()->getId(),
                'cycle_id' => $classe->getCycle()->getId(),
            ]);
        }

        return $this->render('classe/classe_form.html.twig', [
            'titre' => "Ajouter un niveau d'étude",
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }

    private function testUser(Departement $departement)
    {
        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER' or $user->getRolePrincipal() == 'ROLE_SAISIE') {
            if ($user->getDepartement()->getId() != $departement->getId()) {
                $this->addFlash('warning', "Vous n'avez pas accès à ce nivrau d'étude.");
                return false;
            }
        }

        return true;
    }

    /**
     * @Route("/fiche/{id}", name="classe_show", methods="GET")
     */
    public function showAction(Classe $classe)
    {
        $em = $this->getDoctrine()->getManager();
        $ues = $em->getRepository(Ue::class)->getUeByClasse($classe);

        return $this->render('classe/classe_show.html.twig', [
            'classe' => $classe,
            'ues' => $ues,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="classe_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Classe $classe)
    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->remove('cycle');
        $form->remove('code');
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update',
                "Classe Id:{$classe->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Niveau d'étude <b>{$classe->getNom()}</b> modifié avec succès."
            );

            return $this->redirectToRoute('classe_show', [
                'id' => $classe->getId(),
                'cycle_id' => $classe->getCycle()->getId(),
            ]);
        }

        return $this->render('classe/classe_form.html.twig', [
            'titre' => "Modifier le niveau d'étude",
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="classe_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Classe $classe)
    {
        if (count($classe->getUes())) {
            $this->addFlash('warning', "Impossible de supprimer car il y a des éléments asscociés.");
            return $this->redirectToRoute('classe_show', ['id' => $classe->getId()]);
        }
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $classe->getId()],
                'constraints' => new EqualTo($classe->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($classe);
            $logs = new Logs($this->getUser(), 'Delete',
                "Classe Id:{$classe->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success',
                "Niveau d'étude <b>{$classe->getCode()}</b> supprimé avec succès."
            );

            return $this->redirectToRoute('specialite_show', [
                'id' => $classe->getCycle()->getSpecialite()->getId(),
                'cycle_id' => $classe->getCycle()->getId(),
            ]);
        }

        return $this->render('classe/classe_delete.html.twig', [
            'titre' => 'Supprimer la classe',
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }

}
