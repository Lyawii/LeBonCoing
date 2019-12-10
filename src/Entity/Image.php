<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $imagePath;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Advert", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }
    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;
        return $this;
    }
}
