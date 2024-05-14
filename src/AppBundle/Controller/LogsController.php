<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Utils\LogSearch;
use AppBundle\Utils\LogSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Log controller.
 *
 * @Route("logs")
 * @IsGranted("ROLE_DIRECTEUR")
 */
class LogsController extends Controller
{
    /**
     * Lists all log entities.
     *
     * @Route("/", name="logs_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $search = new LogSearch();
        $form = $this->createForm(LogSearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        $qb = $em->createQueryBuilder();
        $param = [
            'debut' => $search->debut->format('Y-m-d 00:00:00'),
            'fin' => $search->fin->format('Y-m-d 23:59:59')
        ];

        $qb->select('log', 'u')
            ->from(Logs::class, 'log')
            ->join('log.user', 'u')
            ->where($qb->expr()->between('log.createAt', ':debut', ':fin'));

        if ($search->user) {
            $qb->andWhere('log.user = :user');
            $param['user'] = $search->user;
        }

        $qb->orderBy('log.createAt', 'desc')
            ->setParameters($param);

        $logs = $qb->getQuery()->getResult();

        return $this->render('logs/index.html.twig', array(
            'titre' => 'Liste des activités',
            'logs' => $logs,
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a new log entity.
     *
     * @Route("/new", name="logs_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        return $this->redirectToRoute('logs_index');

        $log = new Logs();
        $form = $this->createForm('AppBundle\Form\LogsType', $log);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($log);
            $em->flush();

            return $this->redirectToRoute('logs_show', array('id' => $log->getId()));
        }

        return $this->render('logs/new.html.twig', array(
            'log' => $log,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a log entity.
     *
     * @Route("/{id}", name="logs_show")
     * @Method("GET")
     */
    public function showAction(Logs $log)
    {
        return $this->redirectToRoute('logs_index');

        $deleteForm = $this->createDeleteForm($log);

        return $this->render('logs/show.html.twig', array(
            'log' => $log,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing log entity.
     *
     * @Route("/{id}/edit", name="logs_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Logs $log)
    {
        return $this->redirectToRoute('logs_index');

        $deleteForm = $this->createDeleteForm($log);
        $editForm = $this->createForm('AppBundle\Form\LogsType', $log);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('logs_edit', array('id' => $log->getId()));
        }

        return $this->render('logs/edit.html.twig', array(
            'log' => $log,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a log entity.
     *
     * @Route("/{id}", name="logs_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Logs $log)
    {
        return $this->redirectToRoute('logs_index');

        $form = $this->createDeleteForm($log);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($log);
            $em->flush();
        }

        return $this->redirectToRoute('logs_index');
    }

    /**
     * Creates a form to delete a log entity.
     *
     * @param Logs $log The log entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Logs $log)
    {
        return $this->redirectToRoute('logs_index');
        
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('logs_delete', array('id' => $log->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
