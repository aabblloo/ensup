<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="sf3_matiere")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatiereRepository")
 * @UniqueEntity(fields="code", message="Ce code existe déjà.")
 */
class Matiere
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="Ue")
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $ue;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Matiere
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Matiere
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set ue
     *
     * @param \AppBundle\Entity\Ue $ue
     *
     * @return Matiere
     */
    public function setUe(\AppBundle\Entity\Ue $ue = null)
    {
        $this->ue = $ue;

        return $this;
    }

    /**
     * Get ue
     *
     * @return \AppBundle\Entity\Ue
     */
    public function getUe()
    {
        return $this->ue;
    }
}
