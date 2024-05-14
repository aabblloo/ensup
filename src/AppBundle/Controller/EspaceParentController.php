<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Parents;
use AppBundle\Entity\Periode;
use AppBundle\Entity\Suivi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace/parent")
 */
class EspaceParentController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function index0()
    {
        return $this->redirectToRoute('espace_parent_index');
    }

    /**
     * @Route("/index", name="espace_parent_index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Parents::class)->find($this->getUser()->getId());

        if (!$parent) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $etudiants = $em->getRepository(Etudiant::class)->findBy(
                ['parent' => $parent], ['prenom' => 'asc', 'nom' => 'asc']
        );

        return $this->render('espace_parent/espace_parent_index.html.twig', [
                    'parent' => $parent,
                    'etudiants' => $etudiants,
        ]);
    }

    /**
     * @Route("/suivi/{id}", name="espace_parent_suivi")
     */
    public function suivi(Etudiant $etudiant)
    {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Parents::class)->find($this->getUser()->getId());

        if (!$parent->getEtudiants()->contains($etudiant)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $suivis = $em->getRepository(Suivi::class)->findBy([
            'etudiant' => $etudiant], ['date' => 'desc']
        );

        return $this->render('espace_parent/espace_parent_suivi.html.twig', [
                    'parent' => $parent,
                    'etutiant' => $etudiant,
                    'suivis' => $suivis,
        ]);
    }

    /**
     * @Route("/paiement/{id}", name="espace_parent_paiement")
     */
    public function paiement(Etudiant $etudiant)
    {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Parents::class)->find($this->getUser()->getId());

        if (!$parent->getEtudiants()->contains($etudiant)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $query = $em->createQueryBuilder();
        $query->select('ec')
                ->from(EtudiantClasse::class, 'ec')
                ->addSelect('p')
                ->join('ec.paiements', 'p')
                ->where('ec.etudiant = :etudiant')
                ->orderBy('p.date', 'desc')
                ->setParameter('etudiant', $etudiant);
        $etdClasses = $query->getQuery()->getResult();
        return $this->render('espace_etudiant/paiement_print.html.twig', [
                    'etudiant' => $etudiant,
                    'etdClasses' => $etdClasses,
        ]);
    }

    /**
     * @Route("/bulletin/liste/{id}", name="espace_parent_bulletin_liste")
     */
    public function bulletinListe(Etudiant $etudiant)
    {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Parents::class)->find($this->getUser()->getId());

        if (!$parent->getEtudiants()->contains($etudiant)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $db = $this->getDoctrine()->getConnection();
        $stmt = $db->prepare('SELECT * FROM sf3_evaluation_etudiant WHERE eid = :eid');
        $stmt->execute(['eid' => $etudiant->getId()]);
        $bulletins = $stmt->fetchAll();

        return $this->render('espace_parent/espace_parent_bulletin_liste.html.twig', [
                    'etudiant' => $etudiant,
                    'bulletins' => $bulletins,
        ]);
    }

    /**
     * @Route("/bulletin/print/{eid},{aid}/{cid}/{pid}", name="espace_parent_bulletin_print",
     *          requirements={"eid":"\d+", "aid":"\d+", "cid":"\d+", "pid":"\d+"})
     * @ParamConverter("etudiant", options={"id" = "eid"})
     * @ParamConverter("annee", options={"id" = "aid"})
     * @ParamConverter("classe", options={"id" = "cid"})
     * @ParamConverter("periode", options={"id" = "pid"})
     */
    public function bulletinShow(Etudiant $etudiant, AnScolaire $annee, Classe $classe, Periode $periode)
    {
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository(Parents::class)->find($this->getUser()->getId());

        if (!$parent->getEtudiants()->contains($etudiant)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $response = $this->forward('AppBundle\Controller\BulletinController::showAction', [
            'etudiant' => $etudiant,
            'annee' => $annee,
            'classe' => $classe,
            'periode' => $periode
        ]);

        return $response;
    }
    
    /**
     * @Route("/password/change", name="espace_parent_change_pass")
     */
    public function changePassword()
    {
        $response = $this->forward('AppBundle\Controller\UserController::changePasswordAction');

        return $response;
    }

}
