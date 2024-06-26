<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Classe;

/**
 * UeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UeRepository extends \Doctrine\ORM\EntityRepository
{
    public function getUeByClasse(Classe $classe)
    {
        $query = $this->createQueryBuilder('ue');
        $query->join('ue.semestre', 'sem')
            ->where('ue.classe = :classe')
            ->orderBy('ue.nom', 'asc')
            ->orderBy('sem.ordre', 'asc')
            ->setParameter('classe', $classe);

        return $query->getQuery()->getResult();

    }
}
