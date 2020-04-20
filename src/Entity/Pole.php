<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PoleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Pole
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
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="pole", orphanRemoval=true)
     */
    private $category;

    /**
     * @ORM\Column(type="text")
     */
    private $color;

    private $manager;

    

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $labelColor;

    public function __construct()
    {
        $this->category = new ArrayCollection();
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

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Permet d'initialiser la couleur !
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */
    public function initializeColor(){

        $this->color;

        $colors = [

            "bleu" => "#00b8d8",
            "violet" => "#3f20e7",
            "jaune" => "#ffb400",
            "vert" => "#1adba2",
            "rouge" => "#ff4169",
            "noir" => "#0a0c0d"
            
        ];
        


        $this->labelColor = array_search($this->color, $colors);
        
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

   

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
            $category->setPole($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getPole() === $this) {
                $category->setPole(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getLabelColor(): ?string
    {
        return $this->labelColor;
    }

    public function setLabelColor(string $labelColor): self
    {
        $this->labelColor = $labelColor;

        return $this;
    }
}
