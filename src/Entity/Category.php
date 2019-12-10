<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    const CATEGORY = [
        0 => 'Véhicules',
        1 => 'Immobilier',
        2 => 'Vacances',
        3 => 'Loisirs',
        4 => 'Mode',
        5 => 'Multimédia',
        6 => 'Services',
        7 => 'Maison',
        8 => 'Matériel Professionnel',
        9 => 'Divers'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert", mappedBy="category")
     */
    private $advert;

    public function __construct()
    {
        $this->advert = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNameType(): string
    {
        return self::CATEGORY[$this->name];
    }
}
