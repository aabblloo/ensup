<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\Note;
use AppBundle\Form\EvaluationNotesType;
use AppBundle\Form\EvaluationType;
use AppBundle\Utils\ClasseAnnee;
use AppBundle\Utils\ClasseAnneeType;
use http\QueryString;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Evaluation controller.
 *
 * @Route("evaluation")
 */
class EvaluationController extends Controller
{

    /**
     * @Route("/", name="evaluation_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $evaluations = [];
        $search = new ClasseAnnee();
        $form = $this->createForm(ClasseAnneeType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $param['annee'] = $search->getAnnee();
            $param['classe'] = $search->getClasse();

            $query = $em->createQueryBuilder();
            $query->select('e')
                ->from(Evaluation::class, 'e')
                ->join('e.anScolaire', 'a')
                ->join('e.ue', 'ue')
                ->join('ue.classe', 'classe')
                ->andWhere('e.anScolaire = :annee')
                ->andWhere('ue.classe = :classe')
                ->join('ue.semestre', 's');

            // ->andWhere('e.specialite = :specialite')
            // setParameters([
            //     'annee' => $search->getAnnee(),
            //     'classe' => $search->getClasse(),
            //     // 'specialite' => $search->getSpecialite()
            // ]);

            if ($this->getUser()->getRolePrincipal() == 'ROLE_DER') {
                $query->join('classe.cycle', 'cycle')
                    ->join('cycle.specialite', 'specialite')
                    ->join('specialite.section', 'section')
                    ->andWhere('section.departement = :departement');
                $param['departement'] = $this->getUser()->getDepartement();
            }

            $query->orderBy('a.nom', 'desc')
                ->addOrderBy('classe.nom', 'asc')
                ->addOrderBy('s.nom', 'desc')
                ->setParameters($param);
            // $query->setMaxResults(200);
            $evaluations = $query->getQuery()->getResult();
        }


        return $this->render('evaluation/evaluation_index.html.twig', array(
            'evaluations' => $evaluations,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/ajouter", name="evaluation_new")
     */
    public function newAction(Request $request)
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->remove('semestre');
        $form->remove('specialite');
        $form->remove('classe');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();
            $this->addFlash('success', "Evaluation enregistrée avec succès. "
                . "Veuillez entrer les <b>Notes</b> maintenant.");
            return $this->redirectToRoute('evaluation_detail_new', [
                'id' => $evaluation->getId()
            ]);
        }

        return $this->render('evaluation/evaluation_form.html.twig', array(
            'titre' => 'Enregistrer une évaluation',
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="evaluation_show")
     */
    public function showAction(Evaluation $evaluation)
    {
        $deleteForm = $this->createDeleteForm($evaluation);
        $em = $this->getDoctrine()->getManager();

        return $this->render('evaluation/evaluation_show.html.twig', array(
            'titre' => 'Fiche Evaluation',
            'evaluation' => $evaluation,
            // 'evaluation' => $em->getRepository(Evaluation::class)->getEvaluation($evaluation->getId()),
            'notes' => $em->getRepository(Note::class)->getNotesByEvaluation($evaluation),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a evaluation entity.
     *
     * @param Evaluation $evaluation The evaluation entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Evaluation $evaluation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evaluation_delete', array('id' => $evaluation->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/modifier/{id}", name="evaluation_edit")
     */
    public function editAction(Request $request, Evaluation $evaluation)
    {
        // if (count($evaluation->getNotes())) {
        //     $this->addFlash('danger', "La modification est impossible car il y a des notes associées.");
        //     return $this->redirectToRoute('evaluation_show', [
        //         'id' => $evaluation->getId()
        //     ]);
        // }

        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'Evaluation a été modifiée avec succès.");
            return $this->redirectToRoute('evaluation_show', [
                'id' => $evaluation->getId()
            ]);
        }

        return $this->render('evaluation/evaluation_form.html.twig', array(
            'titre' => "Modifier l'évaluation",
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/supprimer/{id}", name="evaluation_delete")
     */
    public function deleteAction(Request $request, Evaluation $evaluation)
    {
        $form = $this->createDeleteForm($evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($evaluation);
            $em->flush();
            $this->addFlash('success', "L'Evaluation a été supprimée avec succès.");
            return $this->redirectToRoute('evaluation_index');
        }

        return $this->render('evaluation/evaluation_delete.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/detailAjouter/{id}", name="evaluation_detail_new")
     */
    public function detailAjouterAction(Request $request, Evaluation $evaluation)
    {
        if (count($evaluation->getNotes())) {
            return $this->redirectToRoute('evaluation_detail_edit', ['id' => $evaluation->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        // $etudiantClasses = $em->getRepository(EtudiantClasse::class)
        //     ->getEtudiantsByClasse($evaluation->getClasse(), $evaluation->getAnScolaire(), $evaluation->getSpecialite());

        $etudiantClasses = $em->getRepository(EtudiantClasse::class)
            ->getEtudiantsByUe($evaluation->getUe(), $evaluation->getAnScolaire());

        if (!$etudiantClasses) {
            $this->addFlash('warning', "Il n'y a pas d'étudiant dans cette classe.");
            return $this->redirectToRoute('evaluation_show', ['id' => $evaluation->getId()]);
        }

        // $classeMatiere = $em->getRepository(ClasseMatiere::class)
        //     ->findOneBy(['classe' => $evaluation->getClasse(), 'matiere' => $evaluation->getMatiere()]);
        //
        // if (!$classeMatiere) {
        //     $this->addFlash('danger', "Il n'y a pas de lien entre la classe et le module.");
        //     return $this->redirectToRoute('evaluation_show', ['id' => $evaluation->getId()]);
        // }

        foreach ($etudiantClasses as $ec) {
            $note = new Note();
            $note->setEvaluation($evaluation);
            $note->setEtudiant($ec->getEtudiant());
            $note->setCredit($evaluation->getUe()->getCredit());

            /* test
            $note->setNoteClasse(rand(7, 18));
            $note->setNoteCompo(rand(7, 18));
            // */

            $evaluation->getNotes()->add($note);
        }

        $form = $this->createForm(EvaluationNotesType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($evaluation);
            $em->flush();
            $this->addFlash('success', 'Notes enregistrées avec succès.');
            return $this->redirectToRoute('evaluation_show', ['id' => $evaluation->getId()]);
        }

        return $this->render('evaluation/evaluation_detail_form.html.twig', array(
            'titre' => 'Enregistrer les notes',
            'evaluation' => $evaluation,
            'notes' => $evaluation->getNotes(),
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/detailModifier/{id}", name="evaluation_detail_edit")
     */
    public function detailModifierAction(Request $request, Evaluation $evaluation)
    {
        if (!count($evaluation->getNotes())) {
            return $this->redirectToRoute('evaluation_detail_new', ['id' => $evaluation->getId()]);
        }

        $em = $this->getDoctrine()->getManager();

        $etudiantClasses = $em->getRepository(EtudiantClasse::class)
            ->getEtudiantsByUe($evaluation->getUe(), $evaluation->getAnScolaire());

        foreach ($etudiantClasses as $ec) {
            $noteEtd = $em->getRepository(Note::class)
                ->findBy(['evaluation' => $evaluation, 'etudiant' => $ec->getEtudiant()]);

            if (count($noteEtd) == 0) {
                $note = new Note();
                $note->setEvaluation($evaluation);
                $note->setEtudiant($ec->getEtudiant());
                $note->setCredit($evaluation->getUe()->getCredit());
                $evaluation->getNotes()->add($note);
            }
        }

        $form = $this->createForm(EvaluationNotesType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($evaluation);
            $em->flush();
            $this->addFlash('success', '<b>Notes</b> modifiées avec succès');
            return $this->redirectToRoute('evaluation_show', ['id' => $evaluation->getId()]);
        }

        return $this->render('evaluation/evaluation_detail_form.html.twig', array(
            'titre' => 'Modifier les notes',
            'evaluation' => $evaluation,
            'notes' => $evaluation->getNotes(),
            'form' => $form->createView()
        ));
    }

}
