<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\MyConfig;
use AppBundle\Utils\ClasseAnneeSeuil;
use AppBundle\Utils\ClasseAnneeSeuilType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListeEtudiantClasseSeuilController extends Controller
{

    /**
     * @Route("/etat/liste_etudiants_par_classe_et_seuil", name="lst_etd_classe_seuil")
     */
    public function indexAction(Request $request)
    {
        $critere = new ClasseAnneeSeuil();

        $form = $this->createForm(ClasseAnneeSeuilType::class, $critere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQueryBuilder();
            $query->select('ec.id, ec.montant, e.prenom, e.nom, e.matricule, sum(coalesce(p.montant,0)) as payer, (ec.montant - sum(coalesce(p.montant,0))) as reste, ((ec.montant * :seuil)/100) as seuil')
                ->from(EtudiantClasse::class, 'ec')
                ->leftJoin('ec.etudiant', 'e')
                ->leftJoin('ec.paiements', 'p')
                ->where('ec.anScolaire = :annee')
                ->andWhere('ec.classe = :classe')
                ->orderBy('e.prenom', 'asc')
                ->orderBy('e.nom', 'asc')
                ->setParameters([
                    'annee' => $critere->annee,
                    'classe' => $critere->classe,
                    'seuil' => $critere->seuil,
                ]);

            if ($critere->lettre) {
                $query->andWhere('ec.lettre = :lettre');
                $query->setParameter('lettre', $critere->lettre);
            }

            $query->groupBy('ec.id, e.id')
                ->having('payer <= seuil');

            $etdClasses = $query->getQuery()->getResult();

            $vue = $this->renderView('etat/liste_etudiant_classe_seuil.html.twig', [
                'critere' => $critere,
                'etdClasses' => $etdClasses,
                'asset' => MyConfig::asset(),
            ]);

            return new Response($vue);

            /*
        $file = 'liste_etudiants_par_classe_et_seuil_' . date('Y_m_d_His') . '.pdf';
        $options = MyConfig::printOption();

        return new PdfResponse(
        $this->get('knp_snappy.pdf')->getOutputFromHtml($vue),
        $file
        );

        // */
        }

        return $this->render('etat/liste_etudiant_classe_seuil_form.html.twig', [
            'titre' => 'Etat - Liste des étudiants par classe',
            'form' => $form->createView(),
        ]);
    }

}
