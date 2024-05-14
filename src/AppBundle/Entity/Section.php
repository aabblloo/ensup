<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Section
 *
 * @ORM\Table(name="sf3_section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @UniqueEntity(fields={"departement", "code"}, errorPath="code")
 * @UniqueEntity(fields={"departement", "nom"}, errorPath="nom")
 */
class Section
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
     * @ORM\Column(name="code", type="string", length=20, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Departement", inversedBy="sections")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank
     */
    private $departement;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Specialite", mappedBy="section")
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    private $specialites;

    public function getNombre(AnScolaire $anScolaire = null)
    {
        $nbre = 0;
        foreach ($this->getSpecialites() as $specialite) {
            $nbre += $specialite->getNombre($anScolaire);
        }
        return $nbre;
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Section
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Section
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \App\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set departement
     *
     * @param \App\Entity\Departement $departement
     *
     * @return Section
     */
    public function setDepartement(\AppBundle\Entity\Departement $departement)
    {
        $this->departement = $departement;

        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->specialites = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add specialite
     *
     * @param \AppBundle\Entity\Specialite $specialite
     *
     * @return Section
     */
    public function addSpecialite(\AppBundle\Entity\Specialite $specialite)
    {
        $this->specialites[] = $specialite;

        return $this;
    }

    /**
     * Remove specialite
     *
     * @param \AppBundle\Entity\Specialite $specialite
     */
    public function removeSpecialite(\AppBundle\Entity\Specialite $specialite)
    {
        $this->specialites->removeElement($specialite);
    }

    /**
     * Get specialites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpecialites()
    {
        return $this->specialites;
    }
}
