<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sf3_depense")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepenseRepository")
 */
class Depense
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
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, options={"default":0})
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AnScolaire")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $anScolaire;

    public static function getTypesDepense()
    {
        return ['Fonctionnement', 'Courantes', 'Autres'];
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
     * @return Depense
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
     * Set ref
     *
     * @param string $ref
     *
     * @return Depense
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Depense
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Depense
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set anScolaire
     *
     * @param \AppBundle\Entity\AnScolaire $anScolaire
     *
     * @return Depense
     */
    public function setAnScolaire(\AppBundle\Entity\AnScolaire $anScolaire)
    {
        $this->anScolaire = $anScolaire;

        return $this;
    }

    /**
     * Get anScolaire
     *
     * @return \AppBundle\Entity\AnScolaire
     */
    public function getAnScolaire()
    {
        return $this->anScolaire;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Depense
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
}
