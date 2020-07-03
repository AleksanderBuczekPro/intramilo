<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 */
class Organization
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sheet", mappedBy="organization")
     */
    private $sheets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Interlocutor", mappedBy="organization", orphanRemoval=true)
     */
    private $interlocutors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="organization")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Attachment", mappedBy="organization")
     */
    private $attachments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoFilename;

    public function __construct()
    {
        $this->sheets = new ArrayCollection();
        $this->interlocutors = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Fonction permettant de récupérer l'adresse formatée de l'organisme
     */
    public function getFullAddress(){

        $fullAddress = $this->address . ' - ' . $this->postcode . ' ' . $this->city;

        return $fullAddress;

    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode): self
    {
        $this->postcode = $postcode;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

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
            $sheet->setOrganization($this);
        }

        return $this;
    }

    public function removeSheet(Sheet $sheet): self
    {
        if ($this->sheets->contains($sheet)) {
            $this->sheets->removeElement($sheet);
            // set the owning side to null (unless already changed)
            if ($sheet->getOrganization() === $this) {
                $sheet->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Interlocutor[]
     */
    public function getInterlocutors(): Collection
    {
        return $this->interlocutors;
    }

    public function addInterlocutor(Interlocutor $interlocutor): self
    {
        if (!$this->interlocutors->contains($interlocutor)) {
            $this->interlocutors[] = $interlocutor;
            $interlocutor->setOrganization($this);
        }

        return $this;
    }

    public function removeInterlocutor(Interlocutor $interlocutor): self
    {
        if ($this->interlocutors->contains($interlocutor)) {
            $this->interlocutors->removeElement($interlocutor);
            // set the owning side to null (unless already changed)
            if ($interlocutor->getOrganization() === $this) {
                $interlocutor->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setOrganization($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getOrganization() === $this) {
                $document->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Attachment[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setOrganization($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getOrganization() === $this) {
                $attachment->setOrganization(null);
            }
        }

        return $this;
    }

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    public function setLogoFilename(?string $logoFilename): self
    {
        $this->logoFilename = $logoFilename;

        return $this;
    }
}
