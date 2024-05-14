<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ue
 *
 * @ORM\Table(name="sf3_ue")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UeRepository")
 * @UniqueEntity(fields={"classe", "code"}, errorPath="code")
 */
class Ue
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
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="smallint", options={"default":1})
     * @Assert\NotBlank()
     */
    private $credit;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Semestre", inversedBy="ues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $semestre;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Classe",inversedBy="ues")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ListeUe")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $listeUe;

    public function getNomComplet()
    {
        return "{$this->nom} - " .
            "{$this->getClasse()->getNom()} - " .
            "{$this->getSemestre()->getNom()}";
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
     * Set classe
     *
     * @param \AppBundle\Entity\Classe $classe
     *
     * @return Ue
     */
    public function setClasse(\AppBundle\Entity\Classe $classe = null)
    {
        $this->classe = $classe;

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
     * Set semestre
     *
     * @param \AppBundle\Entity\Semestre $semestre
     *
     * @return Ue
     */
    public function setSemestre(\AppBundle\Entity\Semestre $semestre)
    {
        $this->semestre = $semestre;

        return $this;
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
     * @return Ue
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
     * @return Ue
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
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
     * @return Ue
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function remplirNom()
    {
        $this->nom = $this->getListeUe()->getNom();
    }

    /**
     * Get listeUe
     *
     * @return \AppBundle\Entity\ListeUe
     */
    public function getListeUe()
    {
        return $this->listeUe;
    }

    /**
     * Set listeUe
     *
     * @param \AppBundle\Entity\ListeUe $listeUe
     *
     * @return Ue
     */
    public function setListeUe(\AppBundle\Entity\ListeUe $listeUe = null)
    {
        $this->listeUe = $listeUe;

        return $this;
    }
}
