<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Lecon
 *
 * @ORM\Table(name="sf3_lecon")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeconRepository")
 */
class Lecon
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
     * @ORM\Column(name="titre", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $titre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="date", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="date", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $fin;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="document", type="string", length=255, nullable=true)
     */
    private $document;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * @ORM\ManyToOne(targetEntity="ClasseMatiere")
     */
    private $classeMatiere;

    /**
     * @Assert\File(
     *      mimeTypes = {"application/pdf", "application/x-pdf"},
     *      maxSize = "20M",
     *      mimeTypesMessage = "Un fichier PDF est requis.",
     * )
     */
    public $docFile;

    /**
     * @Assert\File(
     *      mimeTypes = {"video/mp4"},
     *      maxSize = "2000M",
     *      mimeTypesMessage = "Une vidéo MP4 est requise.",
     * )
     */
    public $videoFile;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Lecon
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
     * Set debut
     *
     * @param \DateTime $debut
     *
     * @return Lecon
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut
     *
     * @return \DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     *
     * @return Lecon
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Lecon
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set document
     *
     * @param string $document
     *
     * @return Lecon
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

    /**
     * Set video
     *
     * @param string $video
     *
     * @return Lecon
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set classeMatiere
     *
     * @param \AppBundle\Entity\ClasseMatiere $classeMatiere
     *
     * @return Lecon
     */
    public function setClasseMatiere(\AppBundle\Entity\ClasseMatiere $classeMatiere = null)
    {
        $this->classeMatiere = $classeMatiere;

        return $this;
    }

    /**
     * Get classeMatiere
     *
     * @return \AppBundle\Entity\ClasseMatiere
     */
    public function getClasseMatiere()
    {
        return $this->classeMatiere;
    }

    public function uploadFile()
    {
        if ($this->docFile === null) {
            return;
        }

        $dossier = realpath('cours') . DIRECTORY_SEPARATOR;
        $extention = strtolower($this->docFile->getClientOriginalExtension());
        if (is_file($dossier . $this->document)) {
            \unlink($dossier . $this->document);
        }
        $this->document = uniqid('', true) . ".{$extention}";
        $this->docFile->move($dossier, $this->document);
        $this->docFile = null;
    }

    public function uploadVideo()
    {
        if ($this->videoFile === null) {
            return;
        }

        $dossier = realpath('cours') . DIRECTORY_SEPARATOR;
        $extention = strtolower($this->videoFile->getClientOriginalExtension());
        if (is_file($dossier . $this->video)) {
            \unlink($dossier . $this->video);
        }
        $this->video = uniqid('', true) . ".{$extention}";
        $this->videoFile->move($dossier, $this->video);
        $this->videoFile = null;
    }
}
