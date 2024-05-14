<?php

namespace AppBundle\Controller\Reports;

use AppBundle\Entity\Etudiant;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\UeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Evaluation controller.
 *
 * @Route("ficheEvaluation")
 */
class FicheEvaluationController extends Controller
{

    /**
     * @Route("/imprimer", name="fiche_evaluation_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('anScolaire', AnneeType::class, [
                'label' => 'Année académique',
                'required' => true
            ])
            ->add('ue', UeType::class, [
                'label' => 'UE',
                'required' => true
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQueryBuilder();
            $query->select('e')
                ->from(Etudiant::class, 'e')
                ->join('e.etudiantClasses', 'ec')
                ->where('ec.classe = :classe')
                ->andWhere('ec.anScolaire = :anScolaire')
                ->orderBy('e.prenom')
                ->addOrderBy('e.nom')
                ->setParameters(array(
                    'classe' => $form->getData()['ue']->getClasse(),
                    'anScolaire' => $form->getData()['anScolaire']
                ));
            $etudiants = $query->getQuery()->getResult();

            return $this->render('etat/fiche_evaluation_imprimer.html.twig', array(
                'ue' => $form->getData()['ue'],
                'anScolaire' => $form->getData()['anScolaire'],
                'etudiants' => $etudiants,
            ));
        }

        return $this->render('etat/fiche_evaluation_form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
