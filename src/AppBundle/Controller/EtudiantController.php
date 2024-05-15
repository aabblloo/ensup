<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Departement;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Logs;
use AppBundle\Entity\MyTest;
use AppBundle\Entity\Suivi;
use AppBundle\Form\EtudiantPhotoType;
use AppBundle\Form\EtudiantsImportType;
use AppBundle\Form\EtudiantType;
use AppBundle\Form\MyType\AnneeType;
use AppBundle\Form\MyType\ClasseType;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @Route("/etudiant")
 */
class EtudiantController extends Controller
{

    /**
     * @Route("/liste", name="etudiant_index", methods="GET|POST")
     */
    public function indexAction(Request $request)
    {
        $etudiants = [];

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
            // ->add('specialite', SpecialiteType::class, [
            //     'placeholder' => 'Sélectionnez une spécialité',
            //     'constraints' => [new NotBlank(), new Valid()]
            // ])
            // ->add('lettre', LettreType::class, [
            //     'placeholder' => 'Sélectionnez une lettre',
            //     'constraints' => [new Valid()], 'required' => false
            // ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data['anScolaire'] = $form->getData()['anScolaire'];
            $data['classe'] = $form->getData()['classe'];
            // $data['specialite'] = $form->getData()['specialite'];

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQueryBuilder();
            $query->select('e')
                ->from(Etudiant::class, 'e')
                ->join('e.etudiantClasses', 'ec')
                ->where('ec.anScolaire = :anScolaire')
                ->andWhere('ec.classe = :classe');
            // ->andWhere('ec.specialite = :specialite');

            /* if ($form->getData()['lettre']) {
                $data['lettre'] = $form->getData()['lettre'];
                $query->andWhere('ec.lettre = :lettre');
            } */

            $query->orderBy('e.nom', ' asc')
                ->addOrderBy('e.prenom', 'asc')
                ->setParameters($data);

            $etudiants = $query->getQuery()->getResult();
        }

        return $this->render('etudiant/etudiant_index.html.twig', [
            'titre' => "Liste des Etudiant(es) par niveau d'étude",
            'etudiants' => $etudiants,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/rechercher", name="etudiant_search", methods="GET|POST")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $etudiants = [];

        $form = $this->createFormBuilder()
            ->setMethod('get')
            ->add('search', SearchType::class, [
                'attr' => [
                    'placeholder' => "Rechercher étudiant Ex: Code Prénom Nom Date naiss…",
                    'class' => 'form-control',
                ],
                'constraints' => [new NotBlank()],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $value = $form->getData()['search'];
            $etudiants = $em->getRepository(Etudiant::class)->search($value);
        }

        return $this->render('etudiant/etudiant_search.html.twig', [
            'titre' => 'Rechercher un(e) étudiant(e)',
            'etudiants' => $etudiants,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajouter", name="etudiant_new", methods="GET|POST")
     */
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $etudiant = new Etudiant();
        // $etudiant = MyTest::generateEtudiant($etudiant);
        $password = $encoder->encodePassword($etudiant, $etudiant->getPasswordText());
        $etudiant->setPassword($password);

        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            do {
                $etudiant->generateCode();
                $res = $em->getRepository(Etudiant::class)->findOneByMatricule($etudiant->getMatricule());
            } while ($res);

            $etudiant->upload();

            if ($etudiant->getDateNaiss()) {
                $etudiant->setDateNaissStr($etudiant->getDateNaiss()->format('d/m/Y'));
            }

            //$etudiant->concat();
            $em->persist($etudiant);
            $em->flush();
            $logs = new Logs($this->getUser(), 'Insert', "Etudiant Id:{$etudiant->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "Etudiant <b>{$etudiant->getPrenomNomMle()}</b> enregistré avec succès.");

            return $this->redirectToRoute('etudiant_show', ['id' => $etudiant->getId()]);
        }

        return $this->render('etudiant/etudiant_form.html.twig', [
            'titre' => 'Enregistrement nouvel étudiant',
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fiche/{id}", name="etudiant_show", methods="GET|POST", requirements={"id":"\d+"})
     */
    public function showAction(Request $request, Etudiant $etudiant, UserPasswordEncoderInterface $encoder)
    {
        if (!$this->testUser($etudiant)) {
            return $this->redirectToRoute('etudiant_index');
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EtudiantPhotoType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etudiant->upload();
            $logs = new Logs($this->getUser(), 'Update', "Changer Photo Etudiant Id:{$etudiant->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "La photo a été changée avec succès.");
            return $this->redirectToRoute('etudiant_show', ['id' => $etudiant->getId()]);
        }

        if (!$etudiant->getLastClasse()) {
            $lastClasse = $em->getRepository(EtudiantClasse::class)->getLastClasse($etudiant);

            if ($lastClasse) {
                $etudiant->setLastClasse($lastClasse->getClasse()->getCode());
                $em->flush();
            }
        }

        if (!$etudiant->getPassword()) {
            $etudiant->generatePassword();
            $password = $encoder->encodePassword($etudiant, $etudiant->getPasswordText());
            $etudiant->setPassword($password);
            $em->flush();
        }

        //var_dump($etudiant);


        return $this->render('etudiant/etudiant_show.html.twig', [
            'titre' => 'Fiche étudiant',
            'etudiant' => $etudiant,
            'paiements' =>$em->getRepository(EtudiantClasse::class)
            ->paiementsAnClasse($etudiant->getId()),
            'classes' => $em->getRepository(EtudiantClasse::class)
                ->findBy(['etudiant' => $etudiant], ['anScolaire' => 'DESC']),
            'dossiers' => $em->getRepository(Dossier::class)
                ->findBy(['etudiant' => $etudiant], ['nom' => 'ASC']),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="etudiant_edit", methods="GET|POST",
     *                          requirements={"id":"\d+"})
     */
    public function editAction(Request $request, Etudiant $etudiant)
    {
        // $this->denyAccessUnlessGranted('ROLE_DIRECTEUR', null, "Accès refusé");

        if (!$this->testUser($etudiant)) {
            return $this->redirectToRoute('etudiant_index');
        }

        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->remove('etudiantClasse');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etudiant->upload();

            if ($etudiant->getDateNaiss()) {
                $etudiant->setDateNaissStr($etudiant->getDateNaiss()->format('d/m/Y'));
            }

            //$etudiant->concat();
            $em = $this->getDoctrine()->getManager();
            $logs = new Logs($this->getUser(), 'Update', "Etudiant Id:{$etudiant->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "L'étudiant <b>{$etudiant->getPrenomNomMle()}</b> "
                . "a été modifié avec succès.");

            return $this->redirectToRoute('etudiant_show', ['id' => $etudiant->getId()]);
        }

        return $this->render('etudiant/etudiant_form.html.twig', [
            'titre' => "Modifier l'étudiant",
            'etudiant' => $etudiant,
            'form' => $form->createView(),
            'isEdit' => true,
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="etudiant_delete", methods="GET|POST",
     *                           requirements={"id":"\d+"})
     */
    public function deleteAction(Etudiant $etudiant, Request $request)
    {
        if (!$this->testUser($etudiant)) {
            return $this->redirectToRoute('etudiant_index');
        }

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, [
                'attr' => ['data' => $etudiant->getId()],
                'constraints' => new EqualTo($etudiant->getId()),
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $res = $this->verify($etudiant);

            if ($this->verify($etudiant)) {
                return $this->redirectToRoute('etudiant_delete', ['id' => $etudiant->getId()]);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($etudiant);
            $logs = new Logs($this->getUser(), 'Delete', "Etudiant Id:{$etudiant->getId()}");
            $em->persist($logs);
            $em->flush();
            $this->addFlash('success', "L'étudiant <b>{$etudiant->getPrenomNomMle()}</b> "
                . "a été supprimé avec succès.");

            return $this->redirectToRoute('etudiant_index');
        }

        return $this->render('etudiant/etudiant_delete.html.twig', [
            'titre' => 'Suppression étudiant(e)',
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }

    private function verify(Etudiant $etudiant)
    {
        $res = false;
        $msg = [];
        $em = $this->getDoctrine()->getManager();

        $suivis = $em->getRepository(Suivi::class)->findBy(['etudiant' => $etudiant]);
        if ($suivis) {
            $res = true;
            $msg[] = 'suivis';
        }

        $etudiantClasses = $em->getRepository(EtudiantClasse::class)->findBy(['etudiant' => $etudiant]);
        if ($etudiantClasses) {
            $res = true;
            $msg[] = 'classes';
        }

        $paiements = $em->getRepository(Suivi::class)->findBy(['etudiant' => $etudiant]);
        if ($paiements) {
            $res = true;
            $msg[] = 'paiements';
        }

        $dossiers = $em->getRepository(Dossier::class)->findOneBy(['etudiant' => $etudiant]);
        if ($dossiers) {
            $res = true;
            $msg[] = 'dossiers';
        }

        if ($msg) {
            $this->addFlash('danger', 'Impossible de supprimer car il y a des éléments asscocés : '
                . implode(', ', $msg));
        }

        return $res;
    }

    private function testUser(Etudiant $etudiant)
    {

        $user = $this->getUser();

        if ($user->getRolePrincipal() == 'ROLE_DER' or $user->getRolePrincipal() == 'ROLE_SAISIE') {

            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $qb->select('e')
                ->from(Etudiant::class, 'e')
                ->join('e.etudiantClasses', 'ec')
                ->join('ec.classe', 'c')
                ->join('ec.anScolaire', 'a')
                ->join('c.cycle', 'cy')
                ->join('cy.specialite', 'sp')
                ->join('sp.section', 'sec')
                ->join('sec.departement', 'd')
                ->where('e = :etudiant')
                ->andWhere('d = :departement')
                ->orderBy('a.nom', 'desc')
                ->setMaxResults(1)
                ->setParameters([
                    'etudiant' => $etudiant,
                    'departement' => $user->getDepartement()
                ]);
            $res = $qb->getQuery()->getResult();

            if (!count($res)) {
                $this->addFlash('warning', "Vous n'avez pas accès à ce niveau d'étude.");
                return false;
            }
        }

        return true;
    }


     /**
     * @Route("/importer", name="etudiant_import", methods="GET|POST")
     */
    public function importAction(Request $request, UserPasswordEncoderInterface $encoder)
    {   
        ini_set('memory_limit', '-1');
        
        $form = $this->createForm(EtudiantsImportType::class);
        $form->handleRequest($request);
        $em = $this->get('doctrine')->getManager();

         if ($form->isSubmitted() && $form->isValid()) {
             $excelFile = $form->get('file')->getData();
             $spreadsheet= IOFactory::load($excelFile);
             $sheet = $spreadsheet->getActiveSheet();
             $rows = $sheet->toArray();
             $rows = array_slice($rows,1);
            
             
             foreach($rows as $r){
                
                $dateNais = new \DateTime($r[3]);
                $etudiant = new Etudiant();
                $etudiant->setPrenom($r[0]) ;
                $etudiant->setnom($r[1]) ;
                $etudiant->setSexe($r[2]) ;
                $etudiant->setLieuNaiss($r[4]) ;
                $etudiant->setQuartier($r[5]) ;
                $etudiant->setTelephone($r[6]) ;
                $etudiant->setEmail($r[7]) ;
                $etudiant->setAnneeBac($r[8]) ;
                $etudiant->setDateNaiss( $dateNais) ;
                do {
                    $etudiant->generateCode();
                    $res = $em->getRepository(Etudiant::class)->findOneByMatricule($etudiant->getMatricule());
                } while ($res);
                $em->persist($etudiant);
             }
                
             $this->addFlash('success', "Importation terminer avec succes.");

         }
         $em->flush();

        return $this->render('etudiant/etudiant_import.html.twig', [
            'titre' => 'Importer la liste des etudiants',
            'form' => $form->createView(),
        ]);
    }
}
