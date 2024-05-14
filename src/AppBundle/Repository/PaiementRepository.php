<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Paiement;
use AppBundle\Utils\EtudiantPeriode;
use Doctrine\ORM\EntityRepository;

class PaiementRepository extends EntityRepository {

    /**
     * @return Paiement[] Liste des paiements par periode et etudiant
     */
    public function findByPeriode(EtudiantPeriode $search) {
        $query = $this->createQueryBuilder('p')
                ->where('p.id is not null')
                ->andWhere('p.date between :debut and :fin')
                ->orderBy('p.date', 'desc')
                ->addOrderBy('p.id', 'desc')
                ->setParameter('debut', $search->getDebut())
                ->setParameter('fin', $search->getFin());

        if ($search->getEtudiant()) {
            $query->andWhere('p.etudiant = :etudiant')
                    ->setParameter('etudiant', $search->getEtudiant());
        }

        return $query->getQuery()->getResult();
    }

}
