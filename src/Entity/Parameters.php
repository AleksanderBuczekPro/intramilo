<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParametersRepository")
 */
class Parameters
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mapUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoFilename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMapUrl(): ?string
    {
        return $this->mapUrl;
    }

    public function setMapUrl(string $mapUrl): self
    {
        $this->mapUrl = $mapUrl;

        return $this;
    }

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    public function setLogoFilename(string $logoFilename): self
    {
        $this->logoFilename = $logoFilename;

        return $this;
    }
}
