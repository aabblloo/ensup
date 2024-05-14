<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="sf3_parent")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParentsRepository")
 * @UniqueEntity(fields="telephone", message="Ce numéro de téléphone existe déjà.")
 * @UniqueEntity(fields="email", message="Cet email existe déjà.")
 */
class Parents implements UserInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *      pattern =   "#^[2-9]\d{7}$#",
     *      message =   "Le format n'est pas correcte."
     * )
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", cascade={"persist", "remove"})
     */
    private $user;

    public function getPrenomNom()
    {
        return "{$this->getPrenom()} {$this->getNom()}";
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Etudiant", mappedBy="parent")
     */
    private $etudiants;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordText;

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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Parents
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Parents
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
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Parents
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Parents
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Parents
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etudiants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     *
     * @return Parents
     */
    public function addEtudiant(\AppBundle\Entity\Etudiant $etudiant)
    {
        $this->etudiants[] = $etudiant;

        return $this;
    }

    /**
     * Remove etudiant
     *
     * @param \AppBundle\Entity\Etudiant $etudiant
     */
    public function removeEtudiant(\AppBundle\Entity\Etudiant $etudiant)
    {
        $this->etudiants->removeElement($etudiant);
    }

    /**
     * Get etudiants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtudiants()
    {
        return $this->etudiants;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->telephone;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        return array_unique(['ROLE_USER', 'ROLE_PARENT']);
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordText(): ?string
    {
        return $this->passwordText;
    }

    public function setPasswordText(?string $passwordText): self
    {
        $this->passwordText = $passwordText;

        return $this;
    }

    public function generatePassword()
    {
        $chaine = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $chiffre = '123456789';
        $code = '';

        for ($i = 1; $i <= 3; $i++) {
            $rd = rand(0, strlen($chaine) - 1);
            $code .= $chaine[$rd];
        }

        for ($i = 1; $i <= 3; $i++) {
            $rd = rand(0, strlen($chiffre) - 1);
            $code .= $chiffre[$rd];
        }

        $this->passwordText = $code;
    }

    public function getName()
    {
        return $this->prenom . ' ' . $this->nom;
    }

}
