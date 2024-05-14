<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Paiement;
use AppBundle\Form\PaiementType;
use AppBundle\Utils\EtudiantPeriode;
use AppBundle\Utils\EtudiantPeriodeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/paiement")
 */
class PaiementController extends Controller
{

    /**
     * @Route("/", name="paiement_index", methods="GET|POST")
     */
    public function indexAction(Request $request)
    {
        $search = new EtudiantPeriode();
        $form = $this->createForm(EtudiantPeriodeType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        $paiements = $this->getDoctrine()->getRepository(Paiement::class)
            ->findByPeriode($search);
        $total = 0;
        foreach ($paiements as $paie) {
            $total += $paie->getMontant();
        }

        return $this->render('paiement/paiement_index.html.twig', [
            'paiements' => $paiements,
            'total' => $total,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajouter", name="paiement_new", methods="GET|POST")
     */
    public function newAction(Request $request)
    {
        $paiement = new Paiement();
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $etdClasse = $em->getRepository(EtudiantClasse::class)->findOneBy([
                'etudiant' => $paiement->getEtudiant(),
                'anScolaire' => $paiement->getAnScolaire(),
            ]);

            if (!$etdClasse) {
                $this->addFlash('danger',
                    "Paiement impossible, cause étudiant non inscrit dans l'année choisie."
                );
                goto Afficher;
                // return $this->redirectToRoute('paiement_new');
            }

            $paiement->setEtudiantClasse($etdClasse);

            if ($this->testDepassement($paiement)) {
                $this->addFlash('danger',
                    "Attention ce paiement provoquera un dépassement du montant dû"
                );
                goto Afficher;
            }

            $em->persist($paiement);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Paiement Id:{$paiement->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Le paiement <b>{$paiement->getRef()}</b> a été enregistré avec succès.");
            return $this->redirectToRoute('paiement_new');
        }

        Afficher:
        return $this->render('paiement/paiement_form.html.twig', [
            'titre' => 'Enregistrer un paiement',
            'paiement' => $paiement,
            'form' => $form->createView(),
        ]);
    }

    public function testDepassement(Paiement $paiement, $oldMontant = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $paiementsTotal = 0;
        $query = $em->createQuery('select sum(p.montant) as total from AppBundle\Entity\Paiement p '
            . 'where p.anScolaire = :annee and p.etudiant = :etudiant')
            ->setParameters([
                'annee' => $paiement->getAnScolaire(),
                'etudiant' => $paiement->getEtudiant()
            ]);
        $res = $query->getOneOrNullResult();
        if ($res) $paiementsTotal = $res['total'];
        $ec = $paiement->getEtudiantClasse();
        //$total = $ec->getPaiementsTotal() + $paiement->getMontant() - $oldMontant;
        $total = (int)$paiementsTotal + $paiement->getMontant() - $oldMontant;
        if ($ec->getMontant() < $total) {
            return true;
        }
        return false;
    }

    /**
     * @Route("/modifier/{id}", name="paiement_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Paiement $paiement)
    {
        $this->denyAccessUnlessGranted('ROLE_DIRECTEUR', null, "Accès refusé");

        $oldMontant = $paiement->getMontant();
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $etdClasse = $em->getRepository(EtudiantClasse::class)->findOneBy([
                'etudiant' => $paiement->getEtudiant(),
                'anScolaire' => $paiement->getAnScolaire(),
            ]);

            if (!$etdClasse) {
                $this->addFlash('danger',
                    "Paiement impossible, cause étudiant non inscrit dans l'année choisie."
                );
                goto Afficher;
                // return $this->redirectToRoute('paiement_new');
            }

            $paiement->setEtudiantClasse($etdClasse);

            if ($this->testDepassement($paiement, $oldMontant)) {
                $this->addFlash(
                    'danger',
                    "Attention ce paiement provoquera un dépassement du montant dû."
                );
                goto Afficher;
            }

            $logs = new Logs($this->getUser(), 'Update', "Paiement Id:{$paiement->getId()}");
            $em->persist($logs);
            $this->addFlash('success', "Le paiement <b>{$paiement->getRef()}</b> a été modifié avec succès.");
            $em->flush();
            return $this->redirectToRoute('paiement_index');
        }

        Afficher:
        return $this->render('paiement/paiement_form.html.twig', [
            'titre' => 'Modifier le paiement',
            'paiement' => $paiement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="paiement_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Paiement $paiement)
    {
        $this->denyAccessUnlessGranted('ROLE_DIRECTEUR', null, "Accès refusé");

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $paiement->getId()],
                'constraints' => new EqualTo($paiement->getId())
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Delete', "Paiement Id:{$paiement->getId()}");
            $em->persist($logs);
            $this->addFlash(
                'success',
                sprintf(
                    'Le paiement <b>%s</b> du <b>%s</b> a été supprimé avec succès.',
                    $paiement->getRef(),
                    $paiement->getDate()->format('d-m-Y')
                )
            );
            $em->remove($paiement);
            $em->flush();
            return $this->redirectToRoute('paiement_index');
        }

        return $this->render('paiement/paiement_delete.html.twig', [
            'titre' => 'Supprimer le paiement',
            'paiement' => $paiement,
            'form' => $form->createView(),
        ]);
    }
}
