<?php

namespace AppBundle\Utils;

use Symfony\Component\Validator\Constraints as Assert;

class ClasseAnneeSeuil
{

    /**
     * @Assert\NotBlank
     */
    public $classe;

    /**
     * @Assert\NotBlank
     */
    public $annee;
    public $lettre;

    /**
     * @Assert\NotBlank
     * @Assert\Choice(callback="getSeuils")
     */
    public $seuil;

    public static function getSeuils()
    {
        return [100, 80, 70, 60, 50];
    }

}
