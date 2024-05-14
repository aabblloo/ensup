<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SuiviRepository extends EntityRepository
{
    public function findByPeriode($debut, $fin, $etudiant)
    {
        $debut = $debut->format('Y-m-d');
        $fin = $fin->format('Y-m-d');
        $criteres = '';
        $param = ['debut' => $debut, 'fin' => $fin];

        if ($etudiant) {
            $criteres = 'AND s.etudiant = :etudiant ';
            $param['etudiant'] = $etudiant;
        }

        $sql = "SELECT s FROM AppBundle\Entity\Suivi s WHERE s.date BETWEEN :debut AND :fin {$criteres} ORDER BY s.date DESC";
        $query = $this->_em->createQuery($sql);
        $query->setParameters($param);
        return $query->getResult();
    }
}
