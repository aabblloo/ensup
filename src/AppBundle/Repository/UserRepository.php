<?php

// src/AppBundle/Repository/UserRepository.php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{

    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.groups', 'g')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function liste()
    {
        return $this->createQueryBuilder('u')
            ->select('u, g')
            ->join('u.groups', 'g')
            ->where('u.username NOT LIKE :username')
            ->andWhere("g.role NOT LIKE 'ROLE_PARENT'")
            ->setParameter('username', 'super')
            ->getQuery()
            ->getResult();
    }

}
