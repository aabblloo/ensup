<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="sf3_classe_matiere")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClasseMatiereRepository")
 * @UniqueEntity(fields={"classe","matiere"},
 *      errorPath="matiere",
 *      message="Cette classe contient déjà ce module.")
 */
class ClasseMatiere
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", options={"default":1})
     * @Assert\NotBlank()
     */
    private $coeff;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Classe")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Matiere")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Assert\Valid()
     */
    private $matiere;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $document;

    /**
     * @Assert\File()
     */
    public $file;

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
     * Set coeff
     *
     * @param integer $coeff
     *
     * @return ClasseMatiere
     */
    public function setCoeff($coeff)
    {
        $this->coeff = $coeff;

        return $this;
    }

    /**
     * Get coeff
     *
     * @return integer
     */
    public function getCoeff()
    {
        return $this->coeff;
    }

    /**
     * Set classe
     *
     * @param \AppBundle\Entity\Classe $classe
     *
     * @return ClasseMatiere
     */
    public function setClasse(\AppBundle\Entity\Classe $classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return \AppBundle\Entity\Classe
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set matiere
     *
     * @param \AppBundle\Entity\Matiere $matiere
     *
     * @return ClasseMatiere
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
     * Set document
     *
     * @param string $document
     *
     * @return EtudiantClasse
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    public function upload()
    {
        if ($this->file === null) {
            return;
        }

        $dossier = realpath('cours') . DIRECTORY_SEPARATOR;
        $extention = strtolower($this->file->getClientOriginalExtension());
        if (is_file($dossier . $this->document)) {
            \unlink($dossier . $this->document);
        }
        $this->document = uniqid('', true) . ".{$extention}";
        $this->file->move($dossier, $this->document);
        $this->file = null;
    }

}
