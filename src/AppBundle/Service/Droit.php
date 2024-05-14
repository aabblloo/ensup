<?php

namespace AppBundle\Service;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
//use Doctrine\ORM\EntityManager;
//use Symfony\Component\HttpFoundation\RequestStack;

class Droit {

    protected $requestStack;
    protected $em;
    protected $user;

    function __construct() {
        //$this->requestStack = $requestStack;
        //$this->em = $em;
    }

    public function Test($value) {
        //$request = $this->requestStack->getCurrentRequest();
        
        if ($value == true) {
            return true;
        } else {
            throw new AccessDeniedHttpException('pas de droit');
        }
    }

}
