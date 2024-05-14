<?php

namespace AppBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;

class EtudiantPeriode {

    private $etudiant;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $debut;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $fin;

    function __construct() {
        $this->debut = new \DateTime(date('1-m-Y'));
        $this->fin = new \DateTime(date('d-m-Y'));
    }

    function getEtudiant() {
        return $this->etudiant;
    }

    function getDebut() {
        return $this->debut;
    }

    function getFin() {
        return $this->fin;
    }

    function setEtudiant($etudiant) {
        $this->etudiant = $etudiant;
    }

    function setDebut($debut) {
        $this->debut = $debut;
    }

    function setFin($fin) {
        $this->fin = $fin;
    }

}
