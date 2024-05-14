<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\Note;
use AppBundle\Entity\Parents;
use AppBundle\Entity\Periode;
use AppBundle\Entity\Semestre;
use AppBundle\Entity\Ue;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\ClasseType;
use AppBundle\Form\SemestreType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
* @Route("/bulletin")
*/
class BulletinController extends Controller
{
    
    /**
    * @Route("/{id}", name="bulletin_index", requirements={"id":"\d+"})
    */
    public function indexAction(Request $request, Etudiant $etudiant)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder([])
        ->setMethod('GET')
        ->add('anScolaire', EntityType::class, [
            'class' => AnScolaire::class,
            'choice_label' => 'nom',
            'placeholder' => '',
            'required' => true
            ])
            ->getForm();
            $form->handleRequest($request);
            
            if ($form->isSubmitted() and $form->isValid()) {
                return $this->redirectToRoute('bulletin_show', ['eid' => $etudiant->getId(), 'aid' => $form->getData()['anScolaire']->getId()]);
            }
            
            // $parent = $em->getRepository(Parents::class)->findOneByUser($this->getUser());
            //
            // if ($parent && !$parent->getEtudiants()->contains($etudiant)) {
                //     throw $this->createAccessDeniedException('Accès non autorisé.');
                // }
                
                return $this->render('bulletin/bulletin_index.html.twig', [
                    'etudiant' => $etudiant,
                    'form' => $form->createView(),
                    'bulletins' => $em->getRepository(Evaluation::class)->getEvaluationsByEtudiant($etudiant)
                ]);
            }
            
            /**
            * @Route("/fiche/{id}", name="bulletin_show", requirements={"id":"\d+"})
            */
            public function showAction(Request $request, EtudiantClasse $etudiantClasse)
            {
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $qb->select('s')
                ->from(Semestre::class, 's')
                ->join('s.ues', 'ue')
                ->join('ue.classe', 'c')
                ->join('c.etudiantClasses', 'ec')
                ->where('ec.id = :ecid')
                ->orderBy('s.id', 'asc')
                ->setParameter('ecid', $etudiantClasse->getId());
                $semestres = $qb->getQuery()->getResult();
                
                $tableau = [];
                $i = 0;
                
                foreach ($semestres as $semestre) {
                    $tableau[$i]['semestre'] = $semestre;
                    $qb = $em->createQueryBuilder();
                    $qb->select('n, e')
                    ->from(Note::class, 'n')
                    ->join('n.evaluation', 'e')
                    ->join('e.ue', 'ue')
                    ->join('ue.semestre', 's')
                    ->where('n.etudiant = :etudiant')
                    ->andWhere('e.anScolaire = :anScolaire')
                    ->andWhere('s = :semestre')
                    ->orderBy('e.ueNom', 'asc')
                    ->setParameters([
                        'etudiant' => $etudiantClasse->getEtudiant(),
                        'anScolaire' => $etudiantClasse->getAnScolaire(),
                        'semestre' => $semestre,
                    ]);
                    
                    $notes = $qb->getQuery()->getResult();
                    
                    $tableau[$i]['notes'] = $notes;
                    
                    $tableau[$i]['moyennes'] = 0;
                    $tableau[$i]['credits'] = 0;
                    $tableau[$i]['moyenne_generale'] = 0;
                    
                    if ($notes and count($notes) > 0) {
                        foreach ($notes as $note) {
                            $tableau[$i]['session'] = $note->getEvaluation()->getSession();
                            $tableau[$i]['moyennes'] += $note->getMoyenne();
                            $tableau[$i]['credits'] += $note->getCredit();
                        }
                        $tableau[$i]['moyenne_generale'] = $tableau[$i]['moyennes'] / count($notes);
                    }
                    $i++;
                }
                
                $moyenne_annuelle = 0;
                // die($tableau[1]['moyenne_generale']);
                
                foreach ($tableau as $tab) {
                    $moyenne_annuelle += $tab['moyenne_generale'];
                }
                
                $moyenne_annuelle = count($tableau) > 0 ? $moyenne_annuelle / count($tableau) : 0;
                
                return $this->render('bulletin/bulletin_show.html.twig', [
                    'etudiant_classe' => $etudiantClasse,
                    'releves' => $tableau,
                    'moyenne_annuelle' => $moyenne_annuelle,
                ]);
            }
            
            /**
            * @Route("/recap/{id}", name="recap_show", requirements={"id":"\d+"})
            */
            public function showRecapAction(Request $request, Etudiant $etudiant)
            {
                $em = $this->getDoctrine()->getManager();
                $etudiantClasses = $etudiant->getEtudiantClasses();
                $tabEtcl = [];
                $tabAnsc = [];
                
                foreach($etudiantClasses as  $i=>$ec){
                    $tabEtcl[$i]=$ec->getId();
                }
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $qb->select('s')
                ->from(Semestre::class, 's')
                ->join('s.ues', 'ue')
                ->join('ue.classe', 'c')
                ->join('c.etudiantClasses', 'ec')
                ->where($qb->expr()->in('ec.id', $tabEtcl))
                ->orderBy('s.id', 'asc');
                $semestres = $qb->getQuery()->getResult();
/* 
                foreach ($semestres as $key => $semestre) {
                    var_dump($semestre->getNom());
                    # code...
                } */
                $tableau = [];
                $i = 0;
                
                foreach ($semestres as $semestre) {
                    $tableau[$i]['semestre'] = $semestre;
                    var_dump($tableau);
                    $qb = $em->createQueryBuilder();
                    $qb->select('n, e')
                    ->from(Note::class, 'n')
                    ->join('n.evaluation', 'e')
                    ->join('e.ue', 'ue')
                    ->join('ue.semestre', 's')
                    ->where('n.etudiant = :etudiant')
                    // ->andWhere('e.anScolaire = :anScolaire')
                    ->andWhere('s = :semestre')
                    ->orderBy('e.ueNom', 'asc')
                    ->setParameters([
                        'etudiant' => $etudiant,
                        // 'anScolaire' => $etudiant->getEtudiantClasses()->getAnScolaire(),
                        'semestre' => $semestre,
                    ]);
                    
                    $notes = $qb->getQuery()->getResult();
                    
                    $tableau[$i]['notes'] = $notes;
                    
                    $tableau[$i]['moyennes'] = 0;
                    $tableau[$i]['credits'] = 0;
                    $tableau[$i]['moyenne_generale'] = 0;
                    
                    if ($notes and count($notes) > 0) {
                        foreach ($notes as $note) {
                            $tableau[$i]['session'] = $note->getEvaluation()->getSession();
                            $tableau[$i]['moyennes'] += $note->getMoyenne();
                            $tableau[$i]['credits'] += $note->getCredit();
                        }
                        $tableau[$i]['moyenne_generale'] = $tableau[$i]['moyennes'] / count($notes);
                    }
                    $i++;
                }
                
                $moyenne_annuelle = 0;
                // die($tableau[1]['moyenne_generale']);
                
                foreach ($tableau as $tab) {
                    $moyenne_annuelle += $tab['moyenne_generale'];
                }
                
                $moyenne_annuelle = count($tableau) > 0 ? $moyenne_annuelle / count($tableau) : 0;
                
                die($tableau);
                
                return $this->render('bulletin/recap_show.html.twig', [
                    'etudiant_classe' => $etudiant->getEtudiantClasses(),
                    'releves' => $tableau,
                    'moyenne_annuelle' => $moyenne_annuelle,
                ]); 
            }
            
            /**
            * @Route("/classe", name="bulletin_classe_form")
            */
            public function classeAction(Request $request)
            {
                $form = $this->createFormBuilder()
                ->setMethod('GET')
                ->add('classe', ClasseType::class, [
                    'placeholder' => "Sélectionnez niveau d'étude",
                    'constraints' => [new NotBlank(), new Valid()]
                    ])
                    ->add('anScolaire', AnneeType::class, [
                        'placeholder' => 'Sélectionnez une année',
                        'constraints' => [new NotBlank(), new Valid()]
                        ])
                        ->add('semestre', \AppBundle\Form\MyType\SemestreType::class, [
                            'placeholder' => 'Sélectionnez un semestre',
                            'constraints' => [new NotBlank(), new Valid()]
                            ])
                            ->getForm();
                            $form->handleRequest($request);
                            
                            if ($form->isSubmitted() and $form->isValid()) {
                                $data = $form->getData();
                                $em = $this->getDoctrine()->getManager();
                                $releves = [];
                                
                                $qb = $em->createQueryBuilder();
                                $qb->select('ec, e')
                                ->from(EtudiantClasse::class, 'ec')
                                ->join('ec.etudiant', 'e')
                                ->where('ec.anScolaire = :anScolaire')
                                ->andWhere('ec.classe = :classe')
                                ->orderBy('e.nom', 'asc')
                                ->addOrderBy('e.prenom', 'asc')
                                ->setParameters([
                                    'anScolaire' => $data['anScolaire'],
                                    'classe' => $data['classe'],
                                ]);
                                $etdClasses = $qb->getQuery()->getResult();
                                
                                $ues = $em->getRepository(Ue::class)
                                ->findBy(['classe' => $data['classe'], 'semestre' => $data['semestre']], ['nom' => 'asc']);
                                
                                $i = 0;
                                foreach ($etdClasses as $etdClasse) {
                                    $releves[$i]['prenom'] = $etdClasse->getEtudiant()->getPrenom();
                                    $releves[$i]['nom'] = $etdClasse->getEtudiant()->getNom();
                                    
                                    if($ues){
                                        foreach ($ues as $ue) {
                                            $releves[$i]['ue'] = $ue->getNom();
                                            $qb = $em->createQueryBuilder();
                                            $qb->select('n, ev')
                                            ->from(Note::class, 'n')
                                            ->join('n.evaluation', 'ev')
                                            ->join('ev.ue', 'ue')
                                            ->join('ue.semestre', 's')
                                            ->where('n.etudiant = :etudiant')
                                            ->andWhere('ue = :ue')
                                            ->andWhere('ev.anScolaire = :anScolaire')
                                            ->andWhere('s = :semestre')
                                            ->orderBy('ev.ueNom', 'asc')
                                            ->setParameters([
                                                'etudiant' => $etdClasse->getEtudiant(),
                                                'anScolaire' => $data['anScolaire'],
                                                'semestre' => $data['semestre'],
                                                'ue' => $ue
                                            ]);
                                            
                                            $note = $qb->getQuery()->getOneOrNullResult();
                                            $releves[$i]['moyennes'][] = $note ? $note->getMoyenne() : null;
                                        }
                                    }else{
                                        $releves[$i]['moyennes'][] = null;
                                    }
                                    $i++;
                                }
                                // die(var_dump($releves));
                                
                                return $this->render('bulletin/bulletin_class_show.html.twig', [
                                    'releves' => $releves,
                                    'ues' => $ues,
                                ]);
                            }
                            
                            return $this->render('bulletin/bulletin_class_form.html.twig', [
                                'form' => $form->createView(),
                            ]);
                        }
                    }
