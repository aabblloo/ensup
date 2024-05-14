<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Depense;
use AppBundle\Entity\Paiement;
use AppBundle\Entity\AnScolaire;
use AppBundle\Form\AnScolaireType;
use AppBundle\Entity\EtudiantClasse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Repository\AnScolaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @Route("/anneeAcademique")
 */
class AnScolaireController extends Controller
{
    /**
     * @Route("/", name="an_scolaire_index", methods="GET")
     */
    public function indexAction()
    {
        return $this->render('an_scolaire/an_scolaire_index.html.twig', [
            'titre' => 'Liste des années académique',
            'an_scolaires' => $this->getDoctrine()
                ->getRepository(AnScolaire::class)
                ->findBy([], ['nom' => 'asc'])
        ]);
    }

    /**
     * @Route("/ajouter", name="an_scolaire_new", methods="GET|POST")
     */
    public function newAction(Request $request)
    {
        $anScolaire = new AnScolaire();
        $form = $this->createForm(AnScolaireType::class, $anScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($anScolaire);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Année académique Id:{$anScolaire->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "L'Année académique <b>{$anScolaire->getNom()}</b> a été créée avec succès.");
            return $this->redirectToRoute('an_scolaire_index');
        }

        return $this->render('an_scolaire/an_scolaire_form.html.twig', [
            'titre' => 'Ajouter une année académique',
            'an_scolaire' => $anScolaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="an_scolaire_edit", methods="GET|POST", requirements={"id":"\d+"})
     */
    public function editAction(Request $request, AnScolaire $anScolaire)
    {
        $form = $this->createForm(AnScolaireType::class, $anScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Année académique Id:{$anScolaire->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "L'Année académique <b>{$anScolaire->getNom()}</b> a été modifiée avec succès.");
            return $this->redirectToRoute('an_scolaire_index');
        }

        return $this->render('an_scolaire/an_scolaire_form.html.twig', [
            'titre' => "Modifier l'année académique",
            'an_scolaire' => $anScolaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="an_scolaire_delete", methods="GET|POST", requirements={"id":"\d+"})
     */
    public function deleteAction(Request $request, AnScolaire $anScolaire)
    {
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $anScolaire->getId()],
                'constraints' => new EqualTo($anScolaire->getId())
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $etudiantClasses = $em->getRepository(EtudiantClasse::class)->findBy(['anScolaire' => $anScolaire]);
            $depenses = $em->getRepository(Depense::class)->findBy(['anScolaire' => $anScolaire]);
            $paiements = $em->getRepository(Paiement::class)->findBy(['anScolaire' => $anScolaire]);
            $cours = $em->getRepository(Cours::class)->findBy(['anScolaire' => $anScolaire]);

            if ($etudiantClasses)
                $this->addFlash('danger', 'Impossible de supprimer car il y a des étudiants associés');

            if ($depenses)
                $this->addFlash('danger', 'Impossible de supprimer car il y a des dépenses associées');

            if ($paiements)
                $this->addFlash('danger', 'Impossible de supprimer car il y a des paiements associés');

            if ($cours)
                $this->addFlash('danger', 'Impossible de supprimer car il y a des cours associés');

            if ($etudiantClasses or $depenses or $paiements or $cours) {
                return $this->redirectToRoute('an_scolaire_delete', ['id' => $anScolaire->getId()]);
            }

            $em->remove($anScolaire);
            $logs = new Logs($this->getUser(), 'Delete', "Année académique Id:{$anScolaire->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "L'Année académique <b>{$anScolaire->getNom()}</b> a été supprimée avec succès.");
            return $this->redirectToRoute('an_scolaire_index');
        }

        return $this->render('an_scolaire/an_scolaire_delete.html.twig', [
            'titre' => "Supprimer l'année académique",
            'anScolaire' => $anScolaire,
            'form' => $form->createView(),
        ]);

    }
}
