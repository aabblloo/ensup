<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Paiement;
use AppBundle\Form\EtudiantClasseType;
use AppBundle\Repository\EtudiantClasseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/etudiant-inscription")
 */
class EtudiantClasseController extends Controller
{

    /**
     * @Route("/", name="etudiant_classe_index", methods="GET")
     */
    public function indexAction(EtudiantClasseRepository $etudiantClasseRepository)
    {
        return $this->render('etudiant_classe/parent_index.html.twig', [
            'etudiant_classes' => $etudiantClasseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajouter/{id}", name="etudiant_classe_new", methods="GET|POST",
     *                         requirements={"id":"\d+"})
     */
    public function newAction(Etudiant $etudiant, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // $annee = $em->getRepository(AnScolaire::class)->getAnneeEnCours();
        $etudiantClasse = new EtudiantClasse();
        $etudiantClasse->setEtudiant($etudiant);
        // $etudiantClasse->setAnScolaire($annee);
        $form = $this->createForm(EtudiantClasseType::class, $etudiantClasse);
        $form->remove('etudiant');
        $form->remove('specialite');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($etudiantClasse);
            if ($etudiantClasse->getMontantPaye() and $etudiantClasse->getMontantPaye() > 0) {
                $paiement = new Paiement();
                $paiement->setEtudiantClasse($etudiantClasse);
                $paiement->setEtudiant($etudiantClasse->getEtudiant());
                $paiement->setAnScolaire($etudiantClasse->getAnScolaire());
                $paiement->setDate($etudiantClasse->getDate());
                $paiement->setMontant($etudiantClasse->getMontantPaye());
                $em->persist($paiement);
            }
            $em->flush();

            $lastClasse = $em->getRepository(EtudiantClasse::class)->getLastClasse($etudiant);

            if ($lastClasse) {
                $etudiant->setLastClasse($lastClasse->getClasse()->getNom());
                $em->flush();
            }

            $logs = new Logs($this->getUser(), 'Insert', "EtudiantClasse Id:{$etudiantClasse->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "La classe <b>{$etudiantClasse->getClasse()->getCode()}</b> a été ajoutée avec succès.");
            return $this->redirectToRoute('etudiant_classe_new', ['id' => $etudiant->getId()]);
        }

        return $this->render('etudiant_classe/etudiant_classe_form.html.twig', [
            'titre' => 'Inscriptions',
            'etudiantClasse' => $etudiantClasse,
            'etudiant' => $etudiant,
            'etudiantClasses' => $em->getRepository(EtudiantClasse::class)
                ->findBy(['etudiant' => $etudiant], ['anScolaire' => 'asc']),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="etudiant_classe_edit", methods="GET|POST")
     */
    public function editAction(Request $request, EtudiantClasse $etudiantClasse)
    {
        $form = $this->createForm(EtudiantClasseType::class, $etudiantClasse);
        $form->remove('etudiant');
        $form->remove('specialite');
        $form->remove('montantPaye');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "EtudiantClasse Id:{$etudiantClasse->getId()}");
            $em->persist($logs);
            $em->flush();

            $etudiant = $etudiantClasse->getEtudiant();
            $lastClasse = $em->getRepository(EtudiantClasse::class)->getLastClasse($etudiant);

            if ($lastClasse) {
                $etudiant->setLastClasse($lastClasse->getClasse()->getCode());
                $em->flush();
            }

            $this->addFlash(
                'success',
                "La classe <b>{$etudiantClasse->getClasse()->getCode()}</b> a été modifiée avec succès."
            );
            return $this->redirectToRoute('etudiant_classe_new', ['id' => $etudiantClasse->getEtudiant()->getId()]);
        }

        return $this->render('etudiant_classe/etudiant_classe_form.html.twig', [
            'titre' => 'Modifier la classe',
            'etudiantClasse' => $etudiantClasse,
            'etudiant' => $etudiantClasse->getEtudiant(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="etudiant_classe_delete",
     *                           methods="GET|POST")
     */
    public function deleteAction(Request $request, EtudiantClasse $etudiantClasse)
    {
        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $etudiantClasse->getId()],
                'constraints' => new EqualTo($etudiantClasse->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Delete', "EtudiantClasse Id:{$etudiantClasse->getId()}");
            $em->persist($logs);
            $em->remove($etudiantClasse);
            $em->flush();
            $this->addFlash('success', "La classe <b>{$etudiantClasse->getClasse()->getCode()}</b> a été supprimée avec succès.");

            return $this->redirectToRoute('etudiant_classe_new', ['id' => $etudiantClasse->getEtudiant()->getId()]);
        }

        return $this->render('etudiant_classe/etudiant_classe_delete.html.twig', [
            'titre' => "Supprimer le niveau d'étude",
            'etudiantClasse' => $etudiantClasse,
            'form' => $form->createView(),
        ]);
    }
}
