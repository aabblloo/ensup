<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sf3_classe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClasseRepository")
 * @UniqueEntity(fields={"cycle", "code"}, errorPath="code")
 * @UniqueEntity(fields={"cycle", "nom"}, errorPath="nom")
 */
class Classe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=0, options={"default":0})
     * @Assert\Type(type="numeric")
     */
    private $taux = 0;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cycle", inversedBy="semestres")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $cycle;

    /**
     * @ORM\OneToMany (targetEntity="AppBundle\Entity\Ue", mappedBy="classe")
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    private $ues;

    /**
     * @ORM\OneToMany (targetEntity="AppBundle\Entity\EtudiantClasse", mappedBy="classe")
     */
    private $etudiantClasses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getNombre(AnScolaire $anScolaire = null)
    {
        $nbre = 0;
        foreach ($this->getEtudiantClasses() as $ec) {
            if ($ec->getAnScolaire()->getId() == $anScolaire->getId()) {
                $nbre++;
            }
        }

        return $nbre;
    }

    /**
     * Get etudiantClasses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtudiantClasses()
    {
        return $this->etudiantClasses;
    }

    public function getCodeNom()
    {
        return "{$this->code} - {$this->nom}";
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
     * @return Classe
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
     * @return Classe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

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

    /**
     * Set taux
     *
     * @param string $taux
     *
     * @return Classe
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;

        return $this;
    }

    /**
     * Get cycle
     *
     * @return \AppBundle\Entity\Cycle
     */
    public function getCycle()
    {
        return $this->cycle;
    }

    /**
     * Set cycle
     *
     * @param \AppBundle\Entity\Cycle $cycle
     *
     * @return Classe
     */
    public function setCycle(\AppBundle\Entity\Cycle $cycle = null)
    {
        $this->cycle = $cycle;

        return $this;
    }

    /**
     * Add ue
     *
     * @param \AppBundle\Entity\Ue $ue
     *
     * @return Classe
     */
    public function addUe(\AppBundle\Entity\Ue $ue)
    {
        $this->ues[] = $ue;

        return $this;
    }

    /**
     * Remove ue
     *
     * @param \AppBundle\Entity\Ue $ue
     */
    public function removeUe(\AppBundle\Entity\Ue $ue)
    {
        $this->ues->removeElement($ue);
    }

    /**
     * Get ues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUes()
    {
        return $this->ues;
    }

    /**
     * Add etudiantClass
     *
     * @param \AppBundle\Entity\EtudiantClasse $etudiantClass
     *
     * @return Classe
     */
    public function addEtudiantClass(\AppBundle\Entity\EtudiantClasse $etudiantClass)
    {
        $this->etudiantClasses[] = $etudiantClass;

        return $this;
    }

    /**
     * Remove etudiantClass
     *
     * @param \AppBundle\Entity\EtudiantClasse $etudiantClass
     */
    public function removeEtudiantClass(\AppBundle\Entity\EtudiantClasse $etudiantClass)
    {
        $this->etudiantClasses->removeElement($etudiantClass);
    }
}
