<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="sf3_dossier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DossierRepository")
 * @UniqueEntity(fields={"nom","etudiant"}, errorPath="nom", message="Ce document existe déjà.")
 * @UniqueEntity(fields={"nom","prof"}, errorPath="nom", message="Ce document existe déjà.")
 */
class Dossier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lien;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Etudiant")
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Professeur")
     */
    private $prof;

    /**
     * @Assert\File
     */
    public $file;

    public static function getFileTypes()
    {
        return [
            'Demande manuscrite',
            "Extrait d'acte de naissance",
            "Attestation du diplôme de l'IFM ou diplôme équivalent",
            'Arrêté de nommination au grade de maitre principal',
            'Autorisation du service employeur',
            'Certificat de nationalité malienne',
            "Copie carte d'identité",
            'Attestation de succès à la licence ou à la maîtrise ou tout diplôme équivalent',
            'Certificat médical',
            'Autre',
            'Tous'
        ];
    }

    public function upload()
    {
        if ($this->file === null)
            return;

        $dossier = realpath('documents') . DIRECTORY_SEPARATOR;
        $extention = strtolower($this->file->getClientOriginalExtension());
        $this->lien = md5(uniqid()) . ".{$extention}";
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Dossier
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
     * Set lien
     *
     * @param string $lien
     *
     * @return Dossier
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

    /**
     * Set etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     *
     * @return Dossier
     */
    public function setEtudiant(\AppBundle\Entity\Etudiant $etudiant = null)
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
     * Set prof
     *
     * @param \AppBundle\Entity\Professeur $prof
     *
     * @return Dossier
     */
    public function setProf(\AppBundle\Entity\Professeur $prof = null)
    {
        $this->prof = $prof;

        return $this;
    }

    /**
     * Get prof
     *
     * @return \AppBundle\Entity\Professeur
     */
    public function getProf()
    {
        return $this->prof;
    }
}
