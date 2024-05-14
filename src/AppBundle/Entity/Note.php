<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Note
 *
 * @ORM\Table(name="sf3_note", uniqueConstraints={
 *      @UniqueConstraint(name="unique_idx",
 *          columns={"evaluation_id", "etudiant_id"})
 * })
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteRepository")
 * @UniqueEntity(fields={"evaluation","etudiant"},
 *      errorPath="etudiant",
 *      message="Les notes de cet étudiant existent déjà pour cette période.")
 */
class Note
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
     * @var float
     *
     * @ORM\Column(name="note_classe", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $noteClasse;

    /**
     * @var float
     *
     * @ORM\Column(name="note_compo", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $noteCompo;

    /**
     * @var integer
     * @ORM\Column(type="smallint", options={"default":1})
     * @Assert\NotBlank()
     */
    private $credit;

    /**
     * @var float
     *
     * @ORM\Column(name="moyenne", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $moyenne;

    /**
     * @var float
     *
     * @ORM\Column(name="moyenne_coeff", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $moyenneCoeff;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resultat;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mention;

    /**
     * @ORM\ManyToOne(targetEntity="Evaluation", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $evaluation;

    /**
     * @ORM\ManyToOne(targetEntity="Etudiant", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $etudiant;

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
     * Get noteClasse
     *
     * @return string
     */
    public function getNoteClasse()
    {
        return $this->noteClasse;
    }

    /**
     * Set noteClasse
     *
     * @param string $noteClasse
     *
     * @return Note
     */
    public function setNoteClasse($noteClasse)
    {
        $this->noteClasse = $noteClasse;

        return $this;
    }

    /**
     * Get noteCompo
     *
     * @return string
     */
    public function getNoteCompo()
    {
        return $this->noteCompo;
    }

    /**
     * Set noteCompo
     *
     * @param string $noteCompo
     *
     * @return Note
     */
    public function setNoteCompo($noteCompo)
    {
        $this->noteCompo = $noteCompo;

        return $this;
    }

    /**
     * Get moyenne
     *
     * @return string
     */
    public function getMoyenne()
    {
        return $this->moyenne;
    }

    /**
     * Set moyenne
     *
     * @param string $moyenne
     *
     * @return Note
     */
    public function setMoyenne($moyenne)
    {
        $this->moyenne = $moyenne;

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
     * Set etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     *
     * @return Note
     */
    public function setEtudiant(\AppBundle\Entity\Etudiant $etudiant = null)
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return \AppBundle\Entity\Evaluation
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * Set evaluation
     *
     * @param \AppBundle\Entity\Evaluation $evaluation
     *
     * @return Note
     */
    public function setEvaluation(\AppBundle\Entity\Evaluation $evaluation = null)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function calculerMoyenne()
    {
        if ($this->noteClasse && $this->noteCompo) {
            $this->moyenne = $this->noteClasse * 0.4 + $this->noteCompo * 0.6;
            $this->moyenneCoeff = $this->moyenne * $this->credit;
            $this->resultat = 'Validée';

            if ($this->moyenne >= 18) $this->mention = 'Excellent';
            elseif ($this->moyenne >= 16) $this->mention = 'Très bien';
            elseif ($this->moyenne >= 14) $this->mention = 'Bien';
            elseif ($this->moyenne >= 12) $this->mention = 'Assez bien';
            elseif ($this->moyenne >= 10) $this->mention = 'Passable';
            else {
                $this->mention = 'Insuffisant';
                $this->resultat = 'Non Validée';
            }
        } else {
            $this->moyenne = null;
            $this->moyenneCoeff = null;
            $this->mention = null;
            $this->resultat = null;
        }
    }

    /**
     * Get credit
     *
     * @return integer
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set credit
     *
     * @param integer $credit
     *
     * @return Note
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get moyenneCoeff
     *
     * @return string
     */
    public function getMoyenneCoeff()
    {
        return $this->moyenneCoeff;
    }

    /**
     * Set moyenneCoeff
     *
     * @param string $moyenneCoeff
     *
     * @return Note
     */
    public function setMoyenneCoeff($moyenneCoeff)
    {
        $this->moyenneCoeff = $moyenneCoeff;

        return $this;
    }

    /**
     * Get resultat
     *
     * @return string
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * Set resultat
     *
     * @param string $resultat
     *
     * @return Note
     */
    public function setResultat($resultat)
    {
        $this->resultat = $resultat;

        return $this;
    }

    /**
     * Get mention
     *
     * @return string
     */
    public function getMention()
    {
        return $this->mention;
    }

    /**
     * Set mention
     *
     * @param string $mention
     *
     * @return Note
     */
    public function setMention($mention)
    {
        $this->mention = $mention;

        return $this;
    }
}
