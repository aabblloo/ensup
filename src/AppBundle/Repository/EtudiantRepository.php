<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class EtudiantRepository extends EntityRepository
{

    /**
     * @return Etudiant[]
     */
    public function search($value)
    {
        $query = "SELECT e.id AS id, CONCAT(e.prenom, ' ', e.nom, ' - ', e.matricule) AS prenomNomMle,
        e.date_naiss, e.telephone, 
        IF(e.photo IS NOT NULL, e.photo, 'default.jpg') AS photoDefault
        FROM sf3_etudiant e 
        WHERE MATCH(e.prenom, e.nom, e.telephone, e.date_naiss_str, e.matricule, e.lieu_naiss, e.quartier, e.last_classe) 
        AGAINST(:value IN BOOLEAN MODE)
        ORDER BY e.nom ASC, e.prenom ASC";

        $db = $this->_em->getConnection();
        $stmt = $db->executeQuery($query, ['value' => $value]);
        return $stmt->fetchAll();

        /*$rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('telephone', 'telephone');
        $rsm->addScalarResult('photoDefault', 'photoDefault');
        $rsm->addScalarResult('prenomNomMle', 'prenomNomMle');
        $rsm->addScalarResult('cycle', 'cycle');
        $rsm->addScalarResult('date_naiss', 'dateNaiss');

        $sql = "SELECT e.id AS id, CONCAT(e.prenom, ' ', e.nom, ' - ', e.matricule) AS prenomNomMle,
        cy.code AS cycle, e.date_naiss, e.telephone, 
        IF(e.photo IS NOT NULL, e.photo, 'default.jpg') AS photoDefault
        FROM sf3_etudiant e JOIN sf3_cycle cy ON e.cycle_id = cy.id 
        WHERE MATCH(e.matricule, e.prenom, e.nom, e.telephone) 
        AGAINST(:value IN BOOLEAN MODE)";
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('value', $value);
        return $query->getResult();*/
    }

    /**
     * @return Etudiant[]
     */
    public function listeParClasse($classe, $anScolaire, $lettre, $specialite = null)
    {
        $criteres = '';
        $param = [
            'classe' => $classe,
            'annee' => $anScolaire,
            // 'specialite' => $specialite
        ];

        // if ($lettre) {
        //     $criteres = 'AND ec.lettre = :lettre';
        //     $param['lettre'] = $lettre;
        // }

        $query = $this->createQueryBuilder('e');
        $query->join('e.etudiantClasses', 'ec')
            ->where('ec.classe = :classe')
            ->andWhere('ec.anScolaire = :annee')// ->andWhere('ec.specialite = :specialite')
        ;

        if ($lettre) {
            $query->andWhere('ec.lettre = :lettre');
            $param['lettre'] = $lettre;
        }

        // $sql = "SELECT e FROM AppBundle\Entity\Etudiant e JOIN e.etudiantClasses ec
        // WHERE ec.classe = :classe AND ec.anScolaire = :anScolaire {$criteres} ORDER BY e.prenom ASC, e.nom ASC";
        // $query = $this->_em->createQuery($sql);
        $query->orderBy('e.nom','asc')->addOrderBy('e.prenom','asc');
        $query->setParameters($param);
        // $query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, true);
        return $query->getQuery()->getResult();
    }

    /**
     * @return Etudiant[]
     */
    public function listeParDepartement($departement, $anScolaire)
    {
        $criteres = '';
        $param = ['departement' => $departement, 'anScolaire' => $anScolaire];

        $sql = "SELECT e FROM AppBundle\Entity\Etudiant e JOIN e.etudiantClasses ec 
            JOIN ec.classe cl
            JOIN 
        WHERE cl.departement = :departement AND ec.anScolaire = :anScolaire {$criteres} ORDER BY e.prenom ASC, e.nom ASC";
        $query = $this->_em->createQuery($sql);
        $query->setParameters($param);
        // $query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, true);
        return $query->getResult();
    }

}
