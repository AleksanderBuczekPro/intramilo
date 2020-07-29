<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterlocutorRepository")
 */
class Interlocutor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"default"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $function;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"default"})
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"default"})
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="interlocutors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sheet", mappedBy="interlocutors")
     * @Groups({"default"})
     */
    private $sheets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $proNumber;
    

    public function __construct()
    {
        $this->sheets = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName()
    {
        return $this->firstName . ' '. $this->lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Collection|Sheet[]
     */
    public function getSheets(): Collection
    {
        return $this->sheets;
    }

    public function addSheet(Sheet $sheet): self
    {
        if (!$this->sheets->contains($sheet)) {
            $this->sheets[] = $sheet;
            $sheet->addInterlocutor($this);
        }

        return $this;
    }

    public function removeSheet(Sheet $sheet): self
    {
        if ($this->sheets->contains($sheet)) {
            $this->sheets->removeElement($sheet);
            $sheet->removeInterlocutor($this);
        }

        return $this;
    }

    public function getProNumber(): ?string
    {
        return $this->proNumber;
    }

    public function setProNumber(?string $proNumber): self
    {
        $this->proNumber = $proNumber;

        return $this;
    }
}
