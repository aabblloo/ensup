<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Parametres;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\NoteType;
use AppBundle\Form\MyType\LettreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class CarteScolaireController extends Controller
{

    /**
     * @Route("/carte_scolaire_par_classe", name="carte_scolaire")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('classe', NoteType::class, [
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->add('annee', AnneeType::class, [
                'label' => 'Année',
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->add('lettre', LettreType::class, [
                'constraints' => [new Valid()],
            ])
            ->add('validite', null, [
                'label' => 'Validité',
                'attr' => ['placeholder' => 'Ex: Octobre 2020'],
                'constraints' => [new NotBlank()],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData();
            $critere = '';
            $param = ['annee' => $search['annee'], 'classe' => $search['classe']];

            $em = $this->getDoctrine()->getManager();

            $qb = $em->createQueryBuilder();
            $qb->select('ec', 'e', 'a', 'cl', 'cy', 'sp')
                ->from(EtudiantClasse::class, 'ec')
                ->join('ec.etudiant', 'e')
                ->join('ec.anScolaire', 'a')
                ->join('ec.classe', 'cl')
                ->join('cl.cycle', 'cy')
                ->join('cy.specialite', 'sp')
                ->where('ec.classe = :classe')
                ->andWhere('ec.anScolaire = :annee')
                ->setParameters($param)
                ->orderBy('e.nom', 'asc')
                ->addOrderBy('e.prenom', 'asc');

            if ($search['lettre']) {
                $param['lettre'] = $search['lettre'];
                $qb->andWhere('ec.lettre = :lettre');
            }

            $cartes = $qb->getQuery()->getResult();

            $signataire = $em->getRepository(Parametres::class)->findOneBy([
                'cle' => 'signateur_carte_etudiant'
            ]);

            return $this->render('carte_etudiant/index.html.twig', [
                'cartes' => $cartes,
                'signataire' => $signataire,
            ]);

        }

        return $this->render('carte_scolaire/form.html.twig', [
            'titre' => 'Etat - Liste des étudiants par classe',
            'form' => $form->createView(),
        ]);
    }

}
