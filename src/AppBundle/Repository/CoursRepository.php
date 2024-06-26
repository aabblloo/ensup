<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Cours;
use AppBundle\Utils\ProfAnneePeriode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * CoursRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CoursRepository extends EntityRepository
{

    /**
     * @return Cours[] Liste des cours par periode et prof
     */
    public function findByPeriode($debut, $fin, $prof, $user = null)
    {
        $debut = $debut->format('Y-m-d');
        $fin = $fin->format('Y-m-d');
        $criteres = '';
        $param = ['debut' => $debut, 'fin' => $fin];

        if ($prof) {
            $criteres .= 'AND c.prof = :prof ';
            $param['prof'] = $prof;
        }

        if ($user) {
            $criteres .= 'AND c.user = :user ';
            $param['user'] = $user;
        }

        $sql = "SELECT c, cl, prof, ue FROM AppBundle\Entity\Cours c "
            . "JOIN c.prof prof "
            . "JOIN c.ue ue "
            . "JOIN ue.classe cl "
            . "WHERE c.date BETWEEN :debut AND :fin "
            . $criteres
            . "ORDER BY c.date DESC";
        $query = $this->_em->createQuery($sql);
        $query->setParameters($param);

        return $query->getResult();
    }

    public function getHonoraireProf($prof, $annee, $mois)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('classe', 'classe');
        $rsm->addScalarResult('ue', 'ue');
        $rsm->addScalarResult('debut', 'debut');
        $rsm->addScalarResult('fin', 'fin');
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('heure', 'heure');
        $rsm->addScalarResult('taux', 'taux');
        $rsm->addScalarResult('montant', 'montant');

        $sql = 'select ue.nom as ue, cl.nom as classe, cr.debut as debut, cr.fin as fin,
                cr.date as date, cr.nbre_heure as heure, cr.taux as taux, 
                (cr.nbre_heure * cr.taux) as montant
            from sf3_cours cr
                JOIN sf3_ue ue ON cr.ue_id = ue.id
                JOIN sf3_classe cl ON ue.classe_id = cl.id
            where month(cr.date) = :mois and cr.prof_id = :prof and cr.an_scolaire_id = :annee
            order by cr.date asc ';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters(
            [
                'prof' => $prof->getId(),
                'annee' => $annee->getId(),
                'mois' => $mois
            ]
        );

        return $query->getResult();
    }

    public function getHonoraireAllProf($annee, $mois)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('prenom', 'prenom');
        $rsm->addScalarResult('nom', 'nom');
        $rsm->addScalarResult('montant', 'montant');

        $sql = 'select prof.prenom as prenom, prof.nom as nom, 
                sum(cr.nbre_heure * cr.taux) as montant
            from sf3_cours cr
                JOIN sf3_professeur prof ON cr.prof_id = prof.id
            where month(cr.date) = :mois and cr.an_scolaire_id = :annee
            group by prof.id
            order by prof.prenom asc, prof.nom asc  ';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters(
            [
                'annee' => $annee->getId(),
                'mois' => $mois
            ]
        );

        return $query->getResult();
    }

    public function getHonoraireProfPeriode(ProfAnneePeriode $search)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('classe', 'classe');
        $rsm->addScalarResult('matiere', 'matiere');
        $rsm->addScalarResult('debut', 'debut');
        $rsm->addScalarResult('fin', 'fin');
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('heure', 'heure');
        $rsm->addScalarResult('taux', 'taux');
        $rsm->addScalarResult('montant', 'montant');

        $sql = 'select cl.code as classe, mat.code as matiere, cr.debut as debut, cr.fin as fin,
                cr.date as date, cr.nbre_heure as heure, cr.taux as taux, 
                (cr.nbre_heure * cr.taux) as montant
            from sf3_cours cr
                JOIN sf3_classe cl ON cr.classe_id = cl.id
                JOIN sf3_matiere mat ON cr.matiere_id = mat.id
            where cr.date between :debut and :fin and cr.prof_id = :prof and cr.an_scolaire_id = :annee
            order by cr.date asc ';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'prof' => $search->getProf()->getId(),
            'annee' => $search->getAnnee()->getId(),
            'debut' => $search->getDebut()->format('Y-m-d'),
            'fin' => $search->getFin()->format('Y-m-d'),
        ]);

        return $query->getResult();
    }

    public function getHonoraireAllProfPeriode(ProfAnneePeriode $search)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('prenom', 'prenom');
        $rsm->addScalarResult('nom', 'nom');
        $rsm->addScalarResult('montant', 'montant');

        $sql = 'select prof.prenom as prenom, prof.nom as nom, 
                sum(cr.nbre_heure * cr.taux) as montant
            from sf3_cours cr
                JOIN sf3_professeur prof ON cr.prof_id = prof.id
            where cr.date between :debut and :fin and cr.an_scolaire_id = :annee
            group by prof.id
            order by prof.prenom asc, prof.nom asc  ';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'annee' => $search->getAnnee()->getId(),
            'debut' => $search->getDebut()->format('Y-m-d'),
            'fin' => $search->getFin()->format('Y-m-d'),
        ]);

        return $query->getResult();
    }

}
