<?php

namespace App\Entity;

use DateTimeZone;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 * fields={"email"},
 * message="Un autre utilisateur s'est déjà inscrit avec cette adresse mail. Merci de la modifier"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre nom de famille")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @Assert\EqualTo(propertyPath="hash", message="Vous n'avez pas correctement confirmé votre mot de passe")
     *
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $introduction;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Antenne", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $antenne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Poste", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poste;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groupe", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Groupe", mappedBy="responsable")
     */
    private $adminGroupes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Website", mappedBy="author", orphanRemoval=true)
     */
    private $websites;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $pictureFilename;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sheet", mappedBy="author")
     */
    private $sheets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="author")
     */
    private $documents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SubCategory", mappedBy="authors")
     */
    private $subCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Attachment", mappedBy="author")
     */
    private $attachments;


    public function getFullName() {

        return $this->firstName .' '. $this->lastName;

     }


     /**
     * Permet d'initialiser le slug !
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */
    public function initializeSlug(){

        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstName . ' ' . $this->lastName);
        }

    }

    /**
     * Permet de formater en majuscule le nom de l'utilisateur
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */
    public function uppercaseLastName(){

        $this->lastName = strtoupper($this->lastName);

    }

    /**
     * Permet de définir la date de création du compte
     * 
     * @ORM\PrePersist
     * @return void
     */
    public function initCreatedAt(){

        $this->setCreatedAt(new \DateTime(null, new DateTimeZone('Europe/Paris')));

    }

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->subCategories = new ArrayCollection();
        $this->adminGroupes = new ArrayCollection();
        $this->websites = new ArrayCollection();
        $this->sheets = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

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



    public function getRoles(){ // est appelé uniquement à la connexion (UserInterface)

        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();
        })->toArray();

        $roles[] = 'ROLE_USER';

        return $roles;
    }

    public function getPassword(){

        return $this->hash;
    }


    public function getSalt() {}


    public function getUsername(){

        return $this->email;
    }
    

    public function eraseCredentials() {}

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }



    /**
     * @return Collection|SubCategory[]
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories[] = $subCategory;
            $subCategory->setAuthor($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): self
    {
        if ($this->subCategories->contains($subCategory)) {
            $this->subCategories->removeElement($subCategory);
            // set the owning side to null (unless already changed)
            if ($subCategory->getAuthor() === $this) {
                $subCategory->setAuthor(null);
            }
        }

        return $this;
    }

    public function getAntenne(): ?Antenne
    {
        return $this->antenne;
    }

    public function setAntenne(?Antenne $antenne): self
    {
        $this->antenne = $antenne;

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


    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getAdminGroupes(): Collection
    {
        return $this->adminGroupes;
    }

    public function addAdminGroupe(Groupe $groupe): self
    {
        if (!$this->adminGroupes->contains($groupe)) {
            $this->adminGroupes[] = $groupe;
            $groupe->setResponsable($this);
        }

        return $this;
    }

    public function removeAdminGroupe(Groupe $groupe): self
    {
        if ($this->adminGroupes->contains($groupe)) {
            $this->adminGroupes->removeElement($groupe);
            // set the owning side to null (unless already changed)
            if ($groupe->getResponsable() === $this) {
                $groupe->setResponsable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Website[]
     */
    public function getWebsites(): Collection
    {
        return $this->websites;
    }

    public function addWebsite(Website $website): self
    {
        if (!$this->websites->contains($website)) {
            $this->websites[] = $website;
            $website->setAuthor($this);
        }

        return $this;
    }

    public function removeWebsite(Website $website): self
    {
        if ($this->websites->contains($website)) {
            $this->websites->removeElement($website);
            // set the owning side to null (unless already changed)
            if ($website->getAuthor() === $this) {
                $website->setAuthor(null);
            }
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
            $sheet->setAuthor($this);
        }

        return $this;
    }

    public function removeSheet(Sheet $sheet): self
    {
        if ($this->sheets->contains($sheet)) {
            $this->sheets->removeElement($sheet);
            // set the owning side to null (unless already changed)
            if ($sheet->getAuthor() === $this) {
                $sheet->setAuthor(null);
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
            $document->setAuthor($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getAuthor() === $this) {
                $document->setAuthor(null);
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
            $attachment->setAuthor($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getAuthor() === $this) {
                $attachment->setAuthor(null);
            }
        }

        return $this;
    }
}
