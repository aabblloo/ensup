<?php

namespace AppBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;

class LogSearch
{
    public $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    public $debut;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    public $fin;

    function __construct()
    {
        $this->debut = new \DateTime(date('1-m-Y'));
        $this->fin = new \DateTime(date('d-m-Y'));
    }
}
