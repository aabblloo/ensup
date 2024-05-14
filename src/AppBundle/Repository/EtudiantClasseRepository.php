<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AnScolaire;
use AppBundle\Entity\Etudiant;
use AppBundle\Entity\EtudiantClasse;
use AppBundle\Entity\Ue;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class EtudiantClasseRepository extends EntityRepository
{

    /**
     * @return EtudiantClasse[] Returns an array of EtudiantClasse objects
     */
    public function paiementsAnClasse($id)
    {
        return $this->createQueryBuilder('ec')
            ->addSelect('a, c')
            ->join('ec.anScolaire', 'a')
            ->join('ec.classe', 'c')
            ->join('c.cycle', 'cy')
            ->join('cy.specialite', 'sp')
            ->join('ec.etudiant', 'e')
            ->orderBy('a.nom', 'desc')
            ->where('e = :etudiant')
            ->setParameter('etudiant', $id)
            ->getQuery()
            ->getResult();
    }

    public function getEtudiantsByClasse($classe, $annee, $specialite)
    {
        $query = $this->createQueryBuilder('ec');
        $query->addSelect('e')
            ->join('ec.etudiant', 'e')
            ->where('ec.classe = :classe')
            ->andWhere('ec.anScolaire = :annee')
            ->andWhere('ec.specialite = :specialite')
            ->orderBy('e.prenom')
            ->addOrderBy('e.nom')
            ->setParameters([
                'classe' => $classe,
                'annee' => $annee,
                'specialite' => $specialite
            ]);
        return $query->getQuery()->getResult();

        // $sql = 'select ec, e from AppBundle:EtudiantClasse ec '
        //     . 'join ec.etudiant e '
        //     . 'where ec.classe = :classe '
        //     . 'and ec.anScolaire = :annee '
        //     . 'and ec.specialite = :specialite '
        //     . 'order by e.prenom asc, e.nom asc';
        // return $query = $this->_em->createQuery($sql)
        //     ->setParameters(['classe' => $classe, 'annee' => $annee, 'specialite' => $specialite])
        //     ->getResult();
    }

    public function getEtudiantsByUe(Ue $ue, AnScolaire $annee)
    {
        $query = $this->createQueryBuilder('ec');
        $query->addSelect('e')
            ->join('ec.etudiant', 'e')
            ->where('ec.classe = :classe')
            ->andWhere('ec.anScolaire = :annee')
            ->orderBy('e.nom')
            ->addOrderBy('e.prenom')
            ->setParameters([
                'classe' => $ue->getClasse(),
                'annee' => $annee,
            ]);
        return $query->getQuery()->getResult();

        // $sql = 'select ec, e from AppBundle:EtudiantClasse ec '
        //     . 'join ec.etudiant e '
        //     . 'where ec.classe = :classe '
        //     . 'and ec.anScolaire = :annee '
        //     . 'and ec.specialite = :specialite '
        //     . 'order by e.prenom asc, e.nom asc';
        // return $query = $this->_em->createQuery($sql)
        //     ->setParameters(['classe' => $classe, 'annee' => $annee, 'specialite' => $specialite])
        //     ->getResult();
    }


    public function getLastClasse(Etudiant $etudiant)
    {
        return $query = $this->createQueryBuilder('ec')
            ->addSelect('a, c')
            ->join('ec.classe', 'c')
            ->join('ec.anScolaire', 'a')
            ->orderBy('a.nom', 'desc')
            ->where('ec.etudiant = :etudiant')
            ->setMaxResults(1)
            ->setParameter('etudiant', $etudiant)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
