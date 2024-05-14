<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sf3_semestre_etudiant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SemestreEtudiantRepository")
 */
class SemestreEtudiant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $noteDevoir;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $noteSem;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $moyenne;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Semestre")
     * @ORM\JoinColumn(nullable=false)
     */
    private $semestre;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Etudiant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Matiere")
     * @ORM\JoinColumn(nullable=false)
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AnScolaire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anScol;


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
     * Set noteDevoir
     *
     * @param string $noteDevoir
     *
     * @return SemestreEtudiant
     */
    public function setNoteDevoir($noteDevoir)
    {
        $this->noteDevoir = $noteDevoir;

        return $this;
    }

    /**
     * Get noteDevoir
     *
     * @return string
     */
    public function getNoteDevoir()
    {
        return $this->noteDevoir;
    }

    /**
     * Set noteSem
     *
     * @param string $noteSem
     *
     * @return SemestreEtudiant
     */
    public function setNoteSem($noteSem)
    {
        $this->noteSem = $noteSem;

        return $this;
    }

    /**
     * Get noteSem
     *
     * @return string
     */
    public function getNoteSem()
    {
        return $this->noteSem;
    }

    /**
     * Set moyenne
     *
     * @param string $moyenne
     *
     * @return SemestreEtudiant
     */
    public function setMoyenne($moyenne)
    {
        $this->moyenne = $moyenne;

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
     * Set semestre
     *
     * @param \AppBundle\Entity\Semestre $semestre
     *
     * @return SemestreEtudiant
     */
    public function setSemestre(\AppBundle\Entity\Semestre $semestre)
    {
        $this->semestre = $semestre;

        return $this;
    }

    /**
     * Get semestre
     *
     * @return \AppBundle\Entity\Semestre
     */
    public function getSemestre()
    {
        return $this->semestre;
    }

    /**
     * Set etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     *
     * @return SemestreEtudiant
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
     * Set matiere
     *
     * @param \AppBundle\Entity\Matiere $matiere
     *
     * @return SemestreEtudiant
     */
    public function setMatiere(\AppBundle\Entity\Matiere $matiere)
    {
        $this->matiere = $matiere;

        return $this;
    }

    /**
     * Get matiere
     *
     * @return \AppBundle\Entity\Matiere
     */
    public function getMatiere()
    {
        return $this->matiere;
    }

    /**
     * Set anScol
     *
     * @param \AppBundle\Entity\AnScolaire $anScol
     *
     * @return SemestreEtudiant
     */
    public function setAnScol(\AppBundle\Entity\AnScolaire $anScol)
    {
        $this->anScol = $anScol;

        return $this;
    }

    /**
     * Get anScol
     *
     * @return \AppBundle\Entity\AnScolaire
     */
    public function getAnScol()
    {
        return $this->anScol;
    }
}
