<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Module
 *
 * @ORM\Table(name="sf3_module")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuleRepository")
 * @UniqueEntity(fields="code", message="Ce code existe déjà.")
 */
class Module
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, nullable=true, unique=true)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $nom;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ue", inversedBy="modules")
     * @ORM\JoinColumn(nullable=false)
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
     * @return Module
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
     * @return Module
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
     * @return Module
     */
    public function setUe(\AppBundle\Entity\Ue $ue)
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
