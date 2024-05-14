<?php

namespace AppBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;

class ProfAnneePeriode {

    private $prof;

    /**
     * @Assert\NotBlank()
     */
    private $annee;

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

    function getProf() {
        return $this->prof;
    }

    function getAnnee() {
        return $this->annee;
    }

    function getDebut() {
        return $this->debut;
    }

    function getFin() {
        return $this->fin;
    }

    function setProf($prof) {
        $this->prof = $prof;
    }

    function setAnnee($annee) {
        $this->annee = $annee;
    }

    function setDebut($debut) {
        $this->debut = $debut;
    }

    function setFin($fin) {
        $this->fin = $fin;
    }

}
