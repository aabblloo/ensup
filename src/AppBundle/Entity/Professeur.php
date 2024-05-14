<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="sf3_professeur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfesseurRepository")
 * @UniqueEntity(fields="telephone", message="Ce numéro de téléphone existe déjà.")
 * @UniqueEntity(fields="email", message="Cet email existe déjà.")
 */
class Professeur
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
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $specialite;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *      pattern =   "#^[2-9]\d{7}$#",
     *      message =   "Le format n'est pas correcte."
     * )
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateEntree;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateSortie;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=0, nullable=true)
     * @Assert\Type(type="numeric")
     */
    private $taux;

    public function getPrenomNom()
    {
        return $this->prenom . ' ' . $this->nom;
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Professeur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Professeur
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
     * Set specialite
     *
     * @param string $specialite
     *
     * @return Professeur
     */
    public function setSpecialite($specialite)
    {
        $this->specialite = $specialite;

        return $this;
    }

    /**
     * Get specialite
     *
     * @return string
     */
    public function getSpecialite()
    {
        return $this->specialite;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Professeur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Professeur
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateEntree
     *
     * @param \DateTime $dateEntree
     *
     * @return Professeur
     */
    public function setDateEntree($dateEntree)
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    /**
     * Get dateEntree
     *
     * @return \DateTime
     */
    public function getDateEntree()
    {
        return $this->dateEntree;
    }

    /**
     * Set dateSortie
     *
     * @param \DateTime $dateSortie
     *
     * @return Professeur
     */
    public function setDateSortie($dateSortie)
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    /**
     * Get dateSortie
     *
     * @return \DateTime
     */
    public function getDateSortie()
    {
        return $this->dateSortie;
    }

    /**
     * Set taux
     *
     * @param string $taux
     *
     * @return Professeur
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;

        return $this;
    }

    /**
     * Get taux
     *
     * @return string
     */
    public function getTaux()
    {
        return $this->taux;
    }
}
