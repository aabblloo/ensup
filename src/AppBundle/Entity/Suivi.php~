<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sf3_suivi")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SuiviRepository")
 */
class Suivi
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getTypes")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $observation;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Etudiant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    public static function getTypes()
    {
        return ['Retard', 'Absence', 'Mauvaise conduite'];
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Suivi
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Suivi
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set observation
     *
     * @param string $observation
     *
     * @return Suivi
     */
    public function setObservation($observation)
    {
        $this->observation = $observation;

        return $this;
    }

    /**
     * Get observation
     *
     * @return string
     */
    public function getObservation()
    {
        return $this->observation;
    }

    /**
     * Set etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     *
     * @return Suivi
     */
    public function setEtudiant(\AppBundle\Entity\Etudiant $etudiant)
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    /**
     * Get etudiant
     *
     * @return \AppBundle\Entity\Etudiant
     */
    public function getEtudiant()
    {
        return $this->etudiant;
    }
}
