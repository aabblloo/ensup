<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sf3_paiement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaiementRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Paiement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @Assert\Date
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, options={"default":0})
     * @Assert\NotBlank()
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AnScolaire")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $anScolaire;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Etudiant")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity="EtudiantClasse", inversedBy="paiements")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $etudiantClasse;

    /**
     * @ORM\PrePersist
     */
    public function pre_persist()
    {
        $date = new DateTime();
        $this->createAt = $date;
        $this->updateAt = $date;
    }

    /**
     * @ORM\PreUpdate
     */
    public function pre_update()
    {
        $date = new DateTime();
        $this->updateAt = $date;
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
     * @return Paiement
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
     * Set montant
     *
     * @param string $montant
     *
     * @return Paiement
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
     * Set createAt
     *
     * @param \DateTime $createAt
     *
     * @return Paiement
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return Paiement
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set anScolaire
     *
     * @param \AppBundle\Entity\AnScolaire $anScolaire
     *
     * @return Paiement
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
     * Set etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     *
     * @return Paiement
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

    /**
     * Set etudiantClasse
     *
     * @param \AppBundle\Entity\EtudiantClasse $etudiantClasse
     *
     * @return Paiement
     */
    public function setEtudiantClasse(\AppBundle\Entity\EtudiantClasse $etudiantClasse = null)
    {
        $this->etudiantClasse = $etudiantClasse;

        return $this;
    }

    /**
     * Get etudiantClasse
     *
     * @return \AppBundle\Entity\EtudiantClasse
     */
    public function getEtudiantClasse()
    {
        return $this->etudiantClasse;
    }
}
