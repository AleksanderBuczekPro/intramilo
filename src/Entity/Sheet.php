<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
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
    public const TYPE = "sheet";
    public const ICON = "<i class='uil uil-bars'></i>";


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\SubCategory", inversedBy="sheets")
     */
    private $subCategory;

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

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="sheets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $organization;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Interlocutor", inversedBy="sheets")
     */
    private $interlocutors;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sheets")
     * @ORM\JoinColumn(nullable=false)
     * @OrderBy({"lastName" = "ASC"})
     * 
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Attachment", mappedBy="sheet", orphanRemoval=true, cascade={"persist"})
     * @OrderBy({"title" = "ASC"})
     */
    private $attachments;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $frontAuthor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Paragraph", mappedBy="sheet", orphanRemoval=true ,cascade={"persist"})
     */
    private $paragraphs;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $introduction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureFilename;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $archivedAt;


    /** Icône d'état de la fiche */
    // private $icon;



    public function __construct()
    {
        $this->headers = new ArrayCollection();

        $this->sheetTools = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tool = new \Doctrine\Common\Collections\ArrayCollection();

        $this->sheetDocuments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->interlocutors = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->paragraphs = new ArrayCollection();


    }

    /**
     * 
     * Permet de retourner l'icône correspondant à son état
     * 
     */
    // public function initializeIcon(){

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

    //     dump($icon);
    //     $this->icon =  $icon;

    // }

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

    


    public function getIcon(): ?int
    {
        return $this->icon;
    }

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

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): self
    {
        $this->subCategory = $subCategory;

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

    /**
     * @return Collection|interlocutor[]
     */
    public function getInterlocutors(): Collection
    {
        return $this->interlocutors;
    }

    public function addInterlocutor(interlocutor $interlocutor): self
    {
        if (!$this->interlocutors->contains($interlocutor)) {
            $this->interlocutors[] = $interlocutor;
        }

        return $this;
    }

    public function removeInterlocutor(interlocutor $interlocutor): self
    {
        if ($this->interlocutors->contains($interlocutor)) {
            $this->interlocutors->removeElement($interlocutor);
        }

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

    /**
     * @return Collection|Attachment[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachments(Attachment $attachments): self
    {
        if (!$this->attachments->contains($attachments)) {
            $this->attachments[] = $attachments;
            $attachments->setSheet($this);
        }

        return $this;
    }

    public function removeAttachments(Attachment $attachments): self
    {
        if ($this->attachments->contains($attachments)) {
            $this->attachments->removeElement($attachments);
            // set the owning side to null (unless already changed)
            if ($attachments->getSheet() === $this) {
                $attachments->setSheet(null);
            }
        }

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

    /**
     * @return Collection|Paragraph[]
     */
    public function getParagraphs(): Collection
    {
        return $this->paragraphs;
    }

    public function addParagraph(Paragraph $paragraph): self
    {
        if (!$this->paragraphs->contains($paragraph)) {
            $this->paragraphs[] = $paragraph;
            $paragraph->setSheet($this);
        }

        return $this;
    }

    public function removeParagraph(Paragraph $paragraph): self
    {
        if ($this->paragraphs->contains($paragraph)) {
            $this->paragraphs->removeElement($paragraph);
            // set the owning side to null (unless already changed)
            if ($paragraph->getSheet() === $this) {
                $paragraph->setSheet(null);
            }
        }

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getPictureFilename(): ?string
    {
        return $this->pictureFilename;
    }

    public function setPictureFilename(?string $pictureFilename): self
    {
        $this->pictureFilename = $pictureFilename;

        return $this;
    }

    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }


}
