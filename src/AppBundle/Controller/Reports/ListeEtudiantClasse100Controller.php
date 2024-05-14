<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\MyConfig;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\NoteType;
use AppBundle\Form\MyType\LettreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class ListeEtudiantClasse100Controller extends Controller
{

    /**
     * @Route("/etat/liste_etudiants_a_100_pr_100", name="lst_etd_classe_100")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
                ->setMethod('GET')
                ->add('classe', NoteType::class, [
                    'constraints' => [new NotBlank(), new Valid()],
                ])
                ->add('annee', AnneeType::class, [
                    'constraints' => [new NotBlank(), new Valid()],
                ])
                ->add('lettre', LettreType::class, [
                    'constraints' => [new Valid()],
                ])
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $search = $form->getData();
            $query = $em->createQueryBuilder();
            $query->select('ec.id, ec.montant, e.prenom, e.nom, e.matricule, sum(coalesce(p.montant,0)) as payer, (ec.montant - sum(coalesce(p.montant,0))) as reste')
                ->from(EtudiantClasse::class, 'ec')
                ->leftJoin('ec.etudiant', 'e')
                ->leftJoin('ec.paiements', 'p')
                ->where('ec.anScolaire = :annee')
                ->andWhere('ec.classe = :classe')
                ->orderBy('e.prenom', 'asc')
                ->orderBy('e.nom', 'asc')
                ->setParameters([
                    'annee' => $search['annee'],
                    'classe' => $search['classe'],
                ]);

            if ($search['lettre']) {
                $query->andWhere('ec.lettre = :lettre');
                $query->setParameter('lettre', $search['lettre']);
            }

            $query->groupBy('ec.id, e.id')
                ->having('payer = ec.montant');

            $etdClasses = $query->getQuery()->getResult();

            $vue = $this->renderView('etat/liste_etudiant_classe_100.html.twig', [
                'search' => $search,
                'etdClasses'=>$etdClasses,
//                'etudiants' => $etudiants,
                'asset' => MyConfig::asset(),
            ]);

            return new Response($vue);
            /*
              $file = 'liste_etudiants_par_classe_100_' . date('Y_m_d_His') . '.pdf';
              $options = MyConfig::printOption();

              return new PdfResponse(
              $this->get('knp_snappy.pdf')->getOutputFromHtml($vue),
              $file
              );

              // */
        }

        return $this->render('etat/liste_etudiant_classe_100_form.html.twig', [
                    'titre' => 'Etat - Liste des étudiants par classe à 100% de paiement',
                    'form' => $form->createView(),
        ]);
    }

}
