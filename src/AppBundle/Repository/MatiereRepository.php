<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Classe;
use Doctrine\ORM\EntityRepository;

class MatiereRepository extends EntityRepository {

    public function getMatieresByClasse(Classe $classe) {
        $sql = 'select cm, m from AppBundle\Entity\ClasseMatiere cm join cm.matiere m where cm.classe = :classe order by m.code';
        return $this->_em->createQuery($sql)
                ->setParameter('classe', $classe)
                ->getResult();
    }

}
