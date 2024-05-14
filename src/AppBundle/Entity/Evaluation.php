<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Note
 *
 * @ORM\Table(name="sf3_evaluation", uniqueConstraints={
 *      @UniqueConstraint(name="unique_idx", columns={"an_scolaire_id", "ue_id"})
 * })
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvaluationRepository")
 * @UniqueEntity(fields={"anScolaire", "ue"}, errorPath="ue")
 */
class Evaluation
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
     * @ORM\ManyToOne(targetEntity="AnScolaire")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank
     */
    private $anScolaire;

    /**
     * @var string
     *
     * @ORM\Column(name="annee", type="string", length=255, nullable=true)
     */
    private $anneeNom;

    /**
     * @ORM\ManyToOne(targetEntity="Ue")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $ue; // matière

    /**
     * @var string
     *
     * @ORM\Column(name="ue_nom", type="string", length=255, nullable=true)
     */
    private $ueNom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $session;

    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param string $session
     * @return Evaluation
     */
    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="evaluation", cascade={"all"})
     */
    private $notes;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function remplirChamp()
    {
        $this->ueNom = $this->getUe()->getNom();
        $this->anneeNom = $this->getAnScolaire()->getNom();
    }

    // /**
    //  * @Assert\Callback
    //  */
    // public function validate(ExecutionContextInterface $context, $payload)
    // {
    //     if ($this->getUe()) {
    //         if ($this->getUe()->getSemestre() != $this->getSemestre()) {
    //             $context->buildViolation("Le semestre ne correspond pas à celui de l'UE")
    //                 ->atPath('semestre')
    //                 ->addViolation();
    //         }
    //
    //         if ($this->getUe()->getSpecialite() != $this->getSpecialite()) {
    //             $context->buildViolation("La spécialité ne correspond pas à celle de l'UE")
    //                 ->atPath('specialite')
    //                 ->addViolation();
    //         }
    //     }
    // }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set anneeNom
     *
     * @param string $anneeNom
     *
     * @return Evaluation
     */
    public function setAnneeNom($anneeNom)
    {
        $this->anneeNom = $anneeNom;

        return $this;
    }

    /**
     * Get anneeNom
     *
     * @return string
     */
    public function getAnneeNom()
    {
        return $this->anneeNom;
    }

    /**
     * Set ueNom
     *
     * @param string $ueNom
     *
     * @return Evaluation
     */
    public function setUeNom($ueNom)
    {
        $this->ueNom = $ueNom;

        return $this;
    }

    /**
     * Get ueNom
     *
     * @return string
     */
    public function getUeNom()
    {
        return $this->ueNom;
    }

    /**
     * Set anScolaire
     *
     * @param \AppBundle\Entity\AnScolaire $anScolaire
     *
     * @return Evaluation
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
     * Set ue
     *
     * @param \AppBundle\Entity\Ue $ue
     *
     * @return Evaluation
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

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Note $note
     *
     * @return Evaluation
     */
    public function addNote(\AppBundle\Entity\Note $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Note $note
     */
    public function removeNote(\AppBundle\Entity\Note $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
