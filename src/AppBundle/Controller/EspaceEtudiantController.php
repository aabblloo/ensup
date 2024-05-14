<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lecon;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Periode;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\ClasseMatiere;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Matiere;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("espace/etudiant")
 */
class EspaceEtudiantController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function index0()
    {
        return $this->redirectToRoute('espace_etudiant_index');
    }

    /**
     * @Route("/index", name="espace_etudiant_index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $etudiant = $em->getRepository(Etudiant::class)->find($this->getUser()->getId());
        $query = $em->createQueryBuilder();
        $query->select('ec')
            ->from(EtudiantClasse::class, 'ec')
            ->addSelect('p')
            ->join('ec.paiements', 'p')
            ->where('ec.etudiant = :etudiant')
            ->orderBy('p.date', 'desc')
            ->setParameter('etudiant', $etudiant);
        $etdClasses = $query->getQuery()->getResult();

        $db = $this->getDoctrine()->getConnection();
        $stmt = $db->prepare('SELECT * FROM sf3_evaluation_etudiant WHERE eid = :eid');
        $stmt->execute(['eid' => $etudiant->getId()]);
        $bulletins = $stmt->fetchAll();

        $cours = array();
        if ($etudiant->getIsAccesCours()) {
            $lastClasse = $em->getRepository(EtudiantClasse::class)->getLastClasse($etudiant);
            $query2 = $em->createQueryBuilder();
            $query2->select('cm')
                ->addSelect('m')
                ->from(ClasseMatiere::class, 'cm')
                ->join('cm.matiere', 'm')
                ->where('cm.classe = :classe')
                ->orderBy('m.code', 'asc')
                ->setParameter('classe', $lastClasse->getClasse());
            $cours = $query2->getQuery()->getResult();
        }

        return $this->render('espace_etudiant/espace_etudiant_index.html.twig', [
            'etudiant' => $etudiant,
            'etdClasses' => $etdClasses,
            'bulletins' => $bulletins,
            'cours' => $cours,
        ]);
    }

    /**
     * @Route("/paiement/print", name="espace_etudiant_paiement_print")
     */
    public function paiement()
    {
        $em = $this->getDoctrine()->getManager();
        $etudiant = $em->getRepository(Etudiant::class)->find($this->getUser()->getId());
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
     * @Route("/bulletin/print/{aid}/{cid}/{pid}", name="espace_etudiant_bulletin_print",
     *          requirements={"aid":"\d+", "cid":"\d+", "pid":"\d+"})
     * @ParamConverter("annee", options={"id" = "aid"})
     * @ParamConverter("classe", options={"id" = "cid"})
     * @ParamConverter("periode", options={"id" = "pid"})
     */
    public function bulletin(AnScolaire $annee, Classe $classe, Periode $periode)
    {
        $em = $this->getDoctrine()->getManager();
        $etudiant = $em->getRepository(Etudiant::class)->find($this->getUser()->getId());

        $response = $this->forward('AppBundle\Controller\BulletinController::showAction', [
            'etudiant' => $etudiant,
            'annee' => $annee,
            'classe' => $classe,
            'periode' => $periode,
        ]);

        return $response;
    }

    /**
     * @Route("/password/change", name="espace_etudiant_change_pass")
     */
    public function changePassword()
    {
        $response = $this->forward('AppBundle\Controller\UserController::changePasswordAction');

        return $response;
    }

    /**
     * @Route("/lecon/{id}", name="espace_etudiant_lecon")
     */
    public function cours(ClasseMatiere $classeMatiere)
    {
        $em = $this->getDoctrine()->getManager();
        $etudiant = $em->getRepository(Etudiant::class)->find($this->getUser()->getId());

        $lecons = array();
        if ($etudiant->getIsAccesCours()) {
            $lastClasse = $em->getRepository(EtudiantClasse::class)->getLastClasse($etudiant);
            if ($classeMatiere->getClasse()->getId() == $lastClasse->getClasse()->getId()) {
                // return new Response($lastClasse->getCode());
                $lecons = $em->getRepository(Lecon::class)->findBy(array('classeMatiere' => $classeMatiere));
            }
        } else {
            $this->addFlash('danger', "Vous n'avez pas accès aux cours.");
            return $this->redirectToRoute('espace_etudiant_index');
        }

        return $this->render('espace_etudiant/espace_etudiant_lecon.html.twig', [
            'etudiant' => $etudiant,
            'classeMatiere' => $classeMatiere,
            'lecons' => $lecons,
        ]);
    }

    /**
     * @Route("/lecon/show/{id}", name="espace_etudiant_lecon_show")
     */
    public function coursShow(Lecon $lecon)
    {
        $classeMatiere = $lecon->getClasseMatiere();

        if (!$lecon->getVideo()) {
            $this->addFlash('success', "Il n'y a pas de vidéo pour cette leçon.");
            return $this->redirectToRoute('lecon_new', array('id' => $classeMatiere->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $etudiant = $em->getRepository(Etudiant::class)->find($this->getUser()->getId());

        if ($etudiant->getIsAccesCours()) {
            $lastClasse = $em->getRepository(EtudiantClasse::class)->getLastClasse($etudiant);
            if ($classeMatiere->getClasse()->getId() != $lastClasse->getClasse()->getId()) {
                $this->addFlash('danger', "Vous n'avez pas accès à cette leçon.");
                return $this->redirectToRoute('espace_etudiant_index');
            }
        } else {
            $this->addFlash('danger', "Vous n'avez pas accès aux cours.");
            return $this->redirectToRoute('espace_etudiant_index');
        }

        return $this->render('espace_etudiant/espace_etudiant_lecon_show.html.twig', [
            'etudiant' => $etudiant,
            'classeMatiere' => $classeMatiere,
            'lecon' => $lecon,
        ]);
    }
}
