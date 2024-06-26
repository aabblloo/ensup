<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Classe;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\Periode;
use Doctrine\ORM\EntityRepository;

/**
 * NoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NoteRepository extends EntityRepository
{

    public function getNotesByEvaluation($evaluation)
    {
        $sql = 'select n, e from AppBundle:Note n '
            . 'join n.etudiant e '
            . 'where n.evaluation = :evaluation '
            . 'order by e.nom asc, e.prenom asc';
        return $query = $this->_em->createQuery($sql)
            ->setParameter('evaluation', $evaluation)
            ->getResult();
    }

    public function getNotesByEtudiantAnneeClassePeriode(
        Etudiant $etudiant, AnScolaire $annee, Classe $classe, Periode $periode)
    {
        $sql = 'select n, e from AppBundle:Note n '
            . 'join n.etudiant e '
            . 'join evaluation ev '
            . 'where n.etudiant = :etudiant '
            . 'and ev.anScolaire = :annee '
            . 'and ev.classe = :classe '
            . 'and ev.periode = :periode '
            . 'order by n.nom asc';
        return $query = $this->_em->createQuery($sql)
            ->setParameter('evaluation', $evaluation)
            ->getResult();
    }

}
