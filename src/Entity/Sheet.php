<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SheetRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Sheet
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
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SubCategory", inversedBy="sheets")
     */
    private $subCategory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organization;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Header", mappedBy="sheet", orphanRemoval=true, cascade={"persist"})
     */
    private $headers;


     /**
     * Les outils (Sheet) de la fiche
     * 
     * @ORM\ManyToMany(targetEntity="App\Entity\Sheet", mappedBy="tool")
     */
    private $sheetTools;

    /**
     * Outil (Sheet) appartenant à une fiche
     * 
     * @ORM\ManyToMany(targetEntity="App\Entity\Sheet", inversedBy="sheetTools")
     * @ORM\JoinTable(name="tools",
     *      joinColumns={@ORM\JoinColumn(name="sheet_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sheet_tool_id", referencedColumnName="id")}
     *      )
     */
    private $tool;

     /**
     * Many Sheet have Many Documents (as sheetDocuments).
     * @ORM\ManyToMany(targetEntity="App\Entity\Document", inversedBy="sheets")
     * @ORM\JoinTable(name="sheet_document")
     */
    private $sheetDocuments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Attachment", mappedBy="sheet", orphanRemoval=true)
     */
    private $attachments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sheet", cascade={"persist", "remove"})
     */
    private $origin;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean")
     */
    private $front;



    public function __construct()
    {
        $this->headers = new ArrayCollection();

        $this->sheetTools = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tool = new \Doctrine\Common\Collections\ArrayCollection();

        $this->sheetDocuments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new ArrayCollection();

    }

    /**
     * Permet d'initialiser le slug !
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */
    public function initializeSlug(){

        
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->title);
        

    }

    // /**
    //  * Callback appelé à chaque fois qu'on crée ou modifie une fiche
    //  * 
    //  * @ORM\PrePersist
    //  * @ORM\PreUpdate
    //  *
    //  * @return void
    //  */
    // public function prePersist()
    // {
    //     if(empty($this->updatedAt))
    //     {
    //         $this->updatedAt = new \DateTime();
    //     }
        

    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Initialisation de l'ID (pour la duplication de la fiche)
     *
     */
    // public function __clone() {
    //     $this->id = null;
    // }

    public function clearId() {
        $this->id = null;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Collection|Header[]
     */
    public function getHeaders(): Collection
    {
        return $this->headers;
    }

    public function addHeader(Header $header): self
    {
        if (!$this->headers->contains($header)) {
            $this->headers[] = $header;
            $header->setSheet($this);
        }

        return $this;
    }

    public function removeHeader(Header $header): self
    {
        if ($this->headers->contains($header)) {
            $this->headers->removeElement($header);
            // set the owning side to null (unless already changed)
            if ($header->getSheet() === $this) {
                $header->setSheet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sheet[]
     */
    public function getSheetTools(): Collection
    {
        return $this->sheetTools;
    }

    public function addSheetTool(Sheet $sheetTool): self
    {
        if (!$this->sheetTools->contains($sheetTool)) {
            $this->sheetTools[] = $sheetTool;
            $sheetTool->addTool($this);
        }

        return $this;
    }

    public function removeSheetTool(Sheet $sheetTool): self
    {
        if ($this->sheetTools->contains($sheetTool)) {
            $this->sheetTools->removeElement($sheetTool);
            $sheetTool->removeTool($this);
        }

        return $this;
    }

    /**
     * @return Collection|Sheet[]
     */
    public function getTool(): Collection
    {
        return $this->tool;
    }

    public function addTool(Sheet $tool): self
    {
        if (!$this->tool->contains($tool)) {
            $this->tool[] = $tool;
        }

        return $this;
    }

    public function removeTool(Sheet $tool): self
    {
        if ($this->tool->contains($tool)) {
            $this->tool->removeElement($tool);
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getSheetDocuments(): Collection
    {
        return $this->sheetDocuments;
    }

    public function addSheetDocument(Document $sheetDocument): self
    {
        if (!$this->sheetDocuments->contains($sheetDocument)) {
            $this->sheetDocuments[] = $sheetDocument;
        }

        return $this;
    }

    public function removeSheetDocument(Document $sheetDocument): self
    {
        if ($this->sheetDocuments->contains($sheetDocument)) {
            $this->sheetDocuments->removeElement($sheetDocument);
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
            $attachment->setSheet($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getSheet() === $this) {
                $attachment->setSheet(null);
            }



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

    public function getOrigin(): ?self
    {
        return $this->origin;
    }

    public function setOrigin(?self $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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


}
