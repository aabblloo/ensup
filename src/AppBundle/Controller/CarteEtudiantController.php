<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Etudiant;
use AppBundle\Entity\Parents;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Parametres;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("carte-etudiant")
 * @IsGranted("ROLE_SCOLARITE")
 */
class CarteEtudiantController extends Controller
{
    /**
     * @Route("/{id}", name="carte_etudiant_index")
     */
    public function indexAction(Request $request, Etudiant $etudiant)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('ec', 'e', 'a', 'cl')
            ->from(EtudiantClasse::class, 'ec')
            ->join('ec.etudiant', 'e')
            ->join('ec.anScolaire', 'a')
            ->join('ec.classe', 'cl')
            //->join('cl.cycle', 'cy')
            //->join('cy.specialite', 'sp')
            ->where('ec.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('a.nom', 'desc')
            ->setMaxResults(1);
        $etdudiantClasse = $qb->getQuery()->getOneOrNullResult();

        $signataire = $em->getRepository(Parametres::class)->findOneBy([
            'cle' => 'signateur_carte_etudiant'
        ]);

        $cartes = [];
        if($etdudiantClasse) $cartes[] = $etdudiantClasse;

        return $this->render('carte_etudiant/index.html.twig', [
            'cartes' => $cartes,
            // 'etudiant_classe' => $etdudiantClasse,
            'signataire' => $signataire,
        ]);
    }
}
