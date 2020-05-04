<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @Vich\Uploadable
 */
class Document
{
    public const TYPE = "document";
    public const ICON = "<i class='uil uil-arrow-up-right'></i>";

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
     * @ORM\ManyToOne(targetEntity="App\Entity\SubCategory", inversedBy="documents")
     */
    private $subCategory;

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
     * Liaison fiche -> document
     * Many Documents have Many Sheets.
     * @ORM\ManyToMany(targetEntity="Sheet", mappedBy="sheetDocuments")
     */
    private $sheets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $front;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $frontAuthor;

    public function __construct()
    {
        $this->sheets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // /**
    //  * 
    //  * Permet de retourner l'icône correspondant à son état
    //  * 
    //  */
    // public function getIcon(){

    //     // Bientôt obsolète
    //     $wellObsolete_start = new DateTime();
    //     $wellObsolete_start->modify('-5 months');

    //     $wellObsolete_end = new DateTime();
    //     $wellObsolete_end->modify('-6 months');

    //     $status = $this->getStatus();
    //     $updatedAt = $this->getUpdatedAt();

    //     // En attente de validation / A corriger
    //     if($status){

    //         // En attente de validation
    //         if($status == "TO_VALIDATE"){

    //             $icon = "<i class='fas fa-pause-circle light'></i>";

    //         }

    //         // A corriger
    //         if($status == "TO_CORRECT"){

    //             $icon = "<i class='fas fa-exclamation-circle rouge'></i>";

    //         }

    //     }else{

    //         // Obsolete
    //         // Supérieur à 6 mois
    //         if($updatedAt <  $wellObsolete_end){

    //             $icon = "<i class='fas fa-times-circle rouge'></i>";


    //         // Entre 5 et 6 mois
    //         }elseif($updatedAt <  $wellObsolete_start){


    //             $icon = "<i class='fas fa-minus-circle orange'></i>";

    //         }

    //     }

    //     return $icon;

    // }


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

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
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
            $sheet->addSheetDocument($this);
        }

        return $this;
    }

    public function removeSheet(Sheet $sheet): self
    {
        if ($this->sheets->contains($sheet)) {
            $this->sheets->removeElement($sheet);
            $sheet->removeSheetDocument($this);
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFront(): ?bool
    {
        return $this->front;
    }

    public function setFront(?bool $front): self
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getFrontAuthor(): ?User
    {
        return $this->frontAuthor;
    }

    public function setFrontAuthor(?User $frontAuthor): self
    {
        $this->frontAuthor = $frontAuthor;

        return $this;
    }

    
}
