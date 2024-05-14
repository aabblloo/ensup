<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\MyConfig;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\ClasseType;
use AppBundle\Form\MyType\NoteType;
use AppBundle\Form\MyType\LettreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class ListeEtudiantClasseController extends Controller
{

    /**
     * @Route("/etat/liste_etudiants_par_classe", name="lst_etd_classe")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('classe', ClasseType::class, [
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->add('anScolaire', AnneeType::class, [
                'constraints' => [new NotBlank(), new Valid()],
            ])
            ->add('lettre', LettreType::class, [
                'constraints' => [new Valid()],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe = $form->getData()['classe'];
            $anScolaire = $form->getData()['anScolaire'];
            $lettre = $form->getData()['lettre'];
            // $specialite = $form->getData()['specialite'];

            $etudiants = $em->getRepository(Etudiant::class)
                ->listeParClasse($classe, $anScolaire, $lettre);

            $vue = $this->renderView('etat/liste_etudiant_classe.html.twig', [
                'titre' => "Liste des étudiants par niveau d'étude",
                'etudiants' => $etudiants,
                'classe' => $classe,
                'anScolaire' => $anScolaire,
                'lettre' => $lettre,
                // 'specialite' => $specialite,
                'asset' => MyConfig::asset(),
            ]);

            return new Response($vue);

            /*
              $file    = 'liste_etudiants_par_classe_'.date('Y_m_d_His').'.pdf';
              $options = MyConfig::printOption();

              return new Response(
              $this->get('knp_snappy.pdf')
              ->getOutputFromHtml($vue, $options, true), 200, [
              'Content-Type'        => 'application/pdf',
              'Content-Disposition' => "inline; filename=\"{$file}\"",
              ]
              );
              // */
        }

        return $this->render('etat/liste_etudiant_classe_form.html.twig', [
            'titre' => "Etat - Liste des étudiants par niveau d'étude",
            'form' => $form->createView(),
        ]);
    }

}
