<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\Departement;
use AppBundle\Entity\MyConfig;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\DepartementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class StatistiqueDepartementController extends Controller
{

    /**
     * @Route("/etat/statistique_par_departement", name="stat_departement")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('departement', DepartementType::class, [
                'required' => false,
                // 'constraints' => [new Valid()],
            ])
            ->add('anScolaire', AnneeType::class, [
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()->getRolePrincipal() == 'ROLE_DER') {
                $departement = $this->getUser()->getDepartement();
            };
            $departement = $form->getData()['departement'];
            $anScolaire = $form->getData()['anScolaire'];
            $param['anScolaire'] = $anScolaire;

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQueryBuilder();
            $query
                ->select('d')
                ->addSelect('sec')
                ->addSelect('sp')
                ->addSelect('cy')
                // ->addSelect($query->expr()->count('ec.etudiant').'as nbre')
                ->from(Departement::class, 'd')
                ->leftJoin('d.sections', 'sec')
                ->leftJoin('sec.specialites', 'sp')
                ->leftJoin('sp.cycles', 'cy')
                ->leftJoin('cy.classes', 'cl')
                ->leftJoin('cl.etudiantClasses', 'ec')
                ->where('ec.anScolaire = :anScolaire')
                ->groupBy('d.id')
                ->addGroupBy('sec.id')
                ->addGroupBy('sp.id')
                ->addGroupBy('cy.id')
                ->orderBy('d.nom')
                ->addOrderBy('sec.nom')
                ->addOrderBy('sp.nom')
                ->addOrderBy('cy.nom');

            if ($departement) {
                $query->andWhere('d = :departement');
                $param['departement'] = $departement;
            }

            $query->setParameters($param);
            $departements = $query->getQuery()->getResult();
            // die(var_dump($departements[0]));

            $vue = $this->renderView(
                'etat/statistique_departement.html.twig', [
                    'titre' => 'Statistique par département',
                    'departement' => $departement,
                    'departements' => $departements,
                    'anScolaire' => $anScolaire,
                    'asset' => MyConfig::asset(),
                ]
            );

            return new Response($vue);
        }


        return $this->render('etat/statistique_departement_form.html.twig', [
            'titre' => 'Etat - Statistique par département',
            'form' => $form->createView(),
        ]);
    }

}
