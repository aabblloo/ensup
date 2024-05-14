<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sf3_cycle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CycleRepository")
 * @UniqueEntity(fields={"code", "specialite"}, errorPath="code")
 */
class Cycle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialite", inversedBy="cycles")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $specialite;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Classe", mappedBy="cycle")
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    private $classes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getNombre(AnScolaire $anScolaire = null)
    {
        $nbre = 0;
        foreach ($this->getClasses() as $classe) {
            $nbre += $classe->getNombre($anScolaire);
        }
        return $nbre;
    }

    /**
     * Get classes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClasses()
    {
        return $this->classes;
    }

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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Cycle
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Cycle
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get specialite
     *
     * @return \AppBundle\Entity\Specialite
     */
    public function getSpecialite()
    {
        return $this->specialite;
    }

    /**
     * Set specialite
     *
     * @param \AppBundle\Entity\Specialite $specialite
     *
     * @return Cycle
     */
    public function setSpecialite(\AppBundle\Entity\Specialite $specialite)
    {
        $this->specialite = $specialite;

        return $this;
    }

    /**
     * Add class
     *
     * @param \AppBundle\Entity\Classe $class
     *
     * @return Cycle
     */
    public function addClass(\AppBundle\Entity\Classe $class)
    {
        $this->classes[] = $class;

        return $this;
    }

    /**
     * Remove class
     *
     * @param \AppBundle\Entity\Classe $class
     */
    public function removeClass(\AppBundle\Entity\Classe $class)
    {
        $this->classes->removeElement($class);
    }
}
