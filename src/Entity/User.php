<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already use."
 * )
 * @UniqueEntity(
 *     fields={"pseudo"},
 *     message="This pseudo is already use."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", minMessage="Your Password must be at least 6 characters long.")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert", mappedBy="user")
     */
    private $adverts;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $information;

    public function __construct()
    {
        $this->adverts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Advert[]
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }
    public function addAdvert(Advert $advert): self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts[] = $advert;
            $advert->setUser($this);
        }
        return $this;
    }
    public function removeAdvert(Advert $advert): self
    {
        if ($this->adverts->contains($advert)) {
            $this->adverts->removeElement($advert);

            if ($advert->getUser() === $this) {
                $advert->setUser(null);
            }
        }
        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

        return $this;
    }

    /**
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return ['ROLE_USER'];
    }

    /** @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
        return $this->getPseudo();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
