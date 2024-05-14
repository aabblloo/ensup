<?php

namespace AppBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;

class ClasseAnnee {

    /**
     * @Assert\NotBlank()
     */
    private $classe;

    /**
     * @Assert\NotBlank()
     */
    private $annee;

    private $specialite;

    /**
     * @return mixed
     */
    public function getSpecialite()
    {
        return $this->specialite;
    }

    /**
     * @param mixed $specialite
     */
    public function setSpecialite($specialite)
    {
        $this->specialite = $specialite;
    }
    
    private $lettre;

    function getClasse() {
        return $this->classe;
    }

    function getAnnee() {
        return $this->annee;
    }

    function getLettre() {
        return $this->lettre;
    }

    function setClasse($classe) {
        $this->classe = $classe;
    }

    function setAnnee($annee) {
        $this->annee = $annee;
    }

    function setLettre($lettre) {
        $this->lettre = $lettre;
    }

}
