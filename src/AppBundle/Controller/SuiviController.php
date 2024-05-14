<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Suivi;
use AppBundle\Form\SuiviType;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\MyConfig;
use AppBundle\Repository\SuiviRepository;
use AppBundle\Repository\EtudiantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @Route("/suivi")
 */
class SuiviController extends Controller
{

    /**
     * @Route("/", name="suivi_index", methods="GET|POST")
     */
    public function indexAction(Request $request)
    {
        $debut    = new \DateTime(date('01-m-Y'));
        $fin      = new \DateTime();
        $etudiant = null;

        $form = $this->createFormBuilder()
            ->add(
                'debut', DateType::class,
                ['widget' => 'single_text', 'data' => $debut]
            )
            ->add(
                'fin', DateType::class,
                ['widget' => 'single_text', 'data' => $fin]
            )
            ->add(
                'etudiant', EntityType::class, [
                    'class'        => Etudiant::class,
                    'choice_value' => 'id',
                    'choice_label' => 'mlePrenomNom',
                    'placeholder'  => '',
                    'required'     => false,
                    'attr'         => ['class'            => 'chosen-select',
                                       'data-placeholder' => MyConfig::CHOSEN_TEXT],
                ]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $debut    = $form->getData()['debut'];
            $fin      = $form->getData()['fin'];
            $etudiant = $form->getData()['etudiant'];
        }

        return $this->render(
            'suivi/suivi_index.html.twig', [
                'titre'  => 'Liste des suivis',
                'suivis' => $this->getDoctrine()->getRepository(Suivi::class)
                    ->findByPeriode($debut, $fin, $etudiant),
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/ajouter/{id}", name="suivi_new", methods="GET|POST",
     *                          requirements={"id":"\d+"})
     */
    public function newAction($id = '', Request $request)
    {
        $em       = $this->getDoctrine()->getManager();
        $suivi    = new Suivi();
        $etudiant = null;

        if ($id) {
            $etudiant = $em->getRepository(Etudiant::class)->find($id);
            if ($etudiant) {
                $suivi->setEtudiant($etudiant);
            }
        }

        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($suivi);
            $em->flush();
            $logs = new Logs(
                $this->getUser(), 'Insert', "Suivi Id:{$suivi->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le suivi du <b>{$suivi->getDate()->format('d-m-Y')}</b> a été enregistré avec succès."
            );
            if ($etudiant) {
                return $this->redirectToRoute(
                    'etudiant_show', ['id' => $etudiant->getId()]
                );
            }

            return $this->redirectToRoute('suivi_index');
        }

        return $this->render(
            'suivi/suivi_form.html.twig', [
                'titre'    => 'Enregistrer un suivi',
                'suivi'    => $suivi,
                'etudiant' => $etudiant,
                'form'     => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/modifier/{id}", name="suivi_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Suivi $suivi)
    {
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em   = $this->getDoctrine()->getManager();
            $logs = new Logs(
                $this->getUser(), 'Update', "Suivi Id:{$suivi->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le suivi du <b>{$suivi->getDate()->format('d-m-Y')}</b> a été modifié avec succès."
            );

            return $this->redirectToRoute('suivi_index');
        }

        return $this->render(
            'suivi/suivi_form.html.twig', [
                'titre'    => 'Modifier le suivi',
                'suivi'    => $suivi,
                'etudiant' => $suivi->getEtudiant(),
                'form'     => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/supprimer/{id}", name="suivi_delete", methods="GET|POST")
     */
    public function deleteAction(Request $request, Suivi $suivi)
    {
        $form = $this->createFormBuilder()
            ->add(
                'id', HiddenType::class, [
                    'attr'        => ['data' => $suivi->getId()],
                    'constraints' => new EqualTo($suivi->getId()),
                ]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($suivi);
            $logs = new Logs(
                $this->getUser(), 'Delete', "Suivi Id:{$suivi->getId()}"
            );
            $em->persist($logs);
            $em->flush();
            $this->addFlash(
                'success',
                "Le suivi du <b>{$suivi->getDate()->format('d-m-Y')}</b> du <b>%s</b> a été supprimé avec succès."
            );

            return $this->redirectToRoute('suivi_index');
        }

        return $this->render(
            'suivi/suivi_delete.html.twig', [
                'titre' => 'Supprimer le suivi',
                'suivi' => $suivi,
                'form'  => $form->createView(),
            ]
        );
    }
}
