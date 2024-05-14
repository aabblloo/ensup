<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AnScolaireRepository extends EntityRepository
{

    public function getAnneeEnCours()
    {
        $annee = $this->_em->createQueryBuilder()
                ->select('p')
                ->from(\AppBundle\Entity\Parametres::class, 'p')
                ->where('p.cle = :cle')
                ->setParameter('cle', 'annee_en_cours')
                ->getQuery()
                ->getOneOrNullResult();

        return $query = $this->createQueryBuilder('a')
                ->orderBy('a.nom', 'desc')
                ->where('a.nom = :nom')
                ->setParameter('nom', $annee->getValeur())
                ->getQuery()
                //->setMaxResults(1)
                ->getOneOrNullResult();
    }

}
