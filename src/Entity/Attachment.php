<?php

namespace App\Entity;

use DateTimeZone;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttachmentRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Attachment
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
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sheet", inversedBy="attachments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sheet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="documentation_files", fileNameProperty="file", size="size", mimeType="mimeType", originalName="originalName")
     * 
     * @var File
     */
    private $genericFile;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mimeType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $front;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="attachments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="attachments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\subCategory", inversedBy="attachments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subCategory;


      /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $genericFile
     */
    public function setGenericFile(?File $genericFile = null): void
    {
        $this->genericFile = $genericFile;

        if (null !== $genericFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }


    public function getGenericFile(): ?File
    {
        return $this->genericFile;
    }


    /**
     * Callback appelé à chaque fois qu'on crée ou modifie une fiche
     * 
     * @ORM\PrePersist
     *
     * @return void
     */
    public function prePersist()
    {
        if(empty($this->updatedAt))
        {
            $this->updatedAt = new \DateTime(null, new DateTimeZone('Europe/Paris'));
        }

        if(empty($this->views))
        {
            $this->views = 0;
        }

        if(empty($this->front))
        {
            $this->front = 0;
        }
        

    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSheet(): ?Sheet
    {
        return $this->sheet;
    }

    public function setSheet(?Sheet $sheet): self
    {
        $this->sheet = $sheet;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getFront(): ?bool
    {
        return $this->front;
    }

    public function setFront(bool $front): self
    {
        $this->front = $front;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSubCategory(): ?subCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?subCategory $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }
}
