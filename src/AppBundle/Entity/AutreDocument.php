<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sf3_autre_document")
 * @ORM\Entity
 */
class AutreDocument
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lien;

    /**
     * @Assert\File
     * @Assert\NotBlank()
     */
    public $file;

    public static function getTypes()
    {
        return ['Document 1', 'Document 2', 'Document 3'];
    }

    public function upload()
    {
        if ($this->file === null) {
            return;
        }

        $dossier = realpath('documents') . DIRECTORY_SEPARATOR;
        $extention = strtolower($this->file->getClientOriginalExtension());
        $this->lien = uniqid('doc_', true) . ".{$extention}";
        $this->file->move($dossier, $this->lien);
        $this->file = null;
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
     * @return AutreDocument
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
     * Set titre
     *
     * @param string $titre
     *
     * @return AutreDocument
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return AutreDocument
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
     * Set lien
     *
     * @param string $lien
     *
     * @return AutreDocument
     */
    public function setLien($lien)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get lien
     *
     * @return string
     */
    public function getLien()
    {
        return $this->lien;
    }
}
