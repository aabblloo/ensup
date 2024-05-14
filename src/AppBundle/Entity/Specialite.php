<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Specialite
 *
 * @ORM\Table(name="sf3_specialite")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpecialiteRepository")
 * @UniqueEntity(fields={"section", "code"}, errorPath="code")
 * @UniqueEntity(fields={"section", "nom"}, errorPath="nom")
 */
class Specialite
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
     * @ORM\Column(name="code", type="string", length=20, nullable=true)
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cycle", mappedBy="specialite")
     */
    private $cycles;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section", inversedBy="specialites")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $section;

    public function getNombre(AnScolaire $anScolaire = null)
    {
        $nbre = 0;
        foreach ($this->getCycles() as $cycle) {
            $nbre += $cycle->getNombre($anScolaire);
        }
        return $nbre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cycles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Specialite
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
     * @return Specialite
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Add cycle
     *
     * @param \AppBundle\Entity\Cycle $cycle
     *
     * @return Specialite
     */
    public function addCycle(\AppBundle\Entity\Cycle $cycle)
    {
        $this->cycles[] = $cycle;

        return $this;
    }

    /**
     * Remove cycle
     *
     * @param \AppBundle\Entity\Cycle $cycle
     */
    public function removeCycle(\AppBundle\Entity\Cycle $cycle)
    {
        $this->cycles->removeElement($cycle);
    }

    /**
     * Get cycles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCycles()
    {
        return $this->cycles;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return Specialite
     */
    public function setSection(\AppBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \AppBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }
}
