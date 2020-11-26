<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AntenneRepository")
 */
class Antenne
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
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="antenne")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hours;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $MondayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $MondayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $MondayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $MondayPmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $TuesdayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $TuesdayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $TuesdayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $TuesdayPmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $WednesdayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $WednesdayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $WednesdayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $WednesdayPmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $ThursdayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $ThursdayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $ThursdayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $ThursdayPmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $FridayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $FridayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $FridayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SaturdayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SaturdayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SaturdayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SaturdayPmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SundayAmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SundayAmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SundayPmOpen;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $SundayPmClose;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $FridayPmClose;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAntenne($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getAntenne() === $this) {
                $user->setAntenne(null);
            }
        }

        return $this;
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

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode): self
    {
        $this->postcode = $postcode;

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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(?string $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getMondayAmOpen(): ?\DateTimeInterface
    {
        return $this->MondayAmOpen;
    }

    public function setMondayAmOpen(?\DateTimeInterface $MondayAmOpen): self
    {
        $this->MondayAmOpen = $MondayAmOpen;

        return $this;
    }

    public function getMondayAmClose(): ?\DateTimeInterface
    {
        return $this->MondayAmClose;
    }

    public function setMondayAmClose(?\DateTimeInterface $MondayAmClose): self
    {
        $this->MondayAmClose = $MondayAmClose;

        return $this;
    }

    public function getMondayPmOpen(): ?\DateTimeInterface
    {
        return $this->MondayPmOpen;
    }

    public function setMondayPmOpen(?\DateTimeInterface $MondayPmOpen): self
    {
        $this->MondayPmOpen = $MondayPmOpen;

        return $this;
    }

    public function getMondayPmClose(): ?\DateTimeInterface
    {
        return $this->MondayPmClose;
    }

    public function setMondayPmClose(?\DateTimeInterface $MondayPmClose): self
    {
        $this->MondayPmClose = $MondayPmClose;

        return $this;
    }

    public function getTuesdayAmOpen(): ?\DateTimeInterface
    {
        return $this->TuesdayAmOpen;
    }

    public function setTuesdayAmOpen(?\DateTimeInterface $TuesdayAmOpen): self
    {
        $this->TuesdayAmOpen = $TuesdayAmOpen;

        return $this;
    }

    public function getTuesdayAmClose(): ?\DateTimeInterface
    {
        return $this->TuesdayAmClose;
    }

    public function setTuesdayAmClose(?\DateTimeInterface $TuesdayAmClose): self
    {
        $this->TuesdayAmClose = $TuesdayAmClose;

        return $this;
    }

    public function getTuesdayPmOpen(): ?\DateTimeInterface
    {
        return $this->TuesdayPmOpen;
    }

    public function setTuesdayPmOpen(?\DateTimeInterface $TuesdayPmOpen): self
    {
        $this->TuesdayPmOpen = $TuesdayPmOpen;

        return $this;
    }

    public function getTuesdayPmClose(): ?\DateTimeInterface
    {
        return $this->TuesdayPmClose;
    }

    public function setTuesdayPmClose(?\DateTimeInterface $TuesdayPmClose): self
    {
        $this->TuesdayPmClose = $TuesdayPmClose;

        return $this;
    }

    public function getWednesdayAmOpen(): ?\DateTimeInterface
    {
        return $this->WednesdayAmOpen;
    }

    public function setWednesdayAmOpen(?\DateTimeInterface $WednesdayAmOpen): self
    {
        $this->WednesdayAmOpen = $WednesdayAmOpen;

        return $this;
    }

    public function getWednesdayAmClose(): ?\DateTimeInterface
    {
        return $this->WednesdayAmClose;
    }

    public function setWednesdayAmClose(?\DateTimeInterface $WednesdayAmClose): self
    {
        $this->WednesdayAmClose = $WednesdayAmClose;

        return $this;
    }

    public function getWednesdayPmOpen(): ?\DateTimeInterface
    {
        return $this->WednesdayPmOpen;
    }

    public function setWednesdayPmOpen(?\DateTimeInterface $WednesdayPmOpen): self
    {
        $this->WednesdayPmOpen = $WednesdayPmOpen;

        return $this;
    }

    public function getWednesdayPmClose(): ?\DateTimeInterface
    {
        return $this->WednesdayPmClose;
    }

    public function setWednesdayPmClose(?\DateTimeInterface $WednesdayPmClose): self
    {
        $this->WednesdayPmClose = $WednesdayPmClose;

        return $this;
    }

    public function getThursdayAmOpen(): ?\DateTimeInterface
    {
        return $this->ThursdayAmOpen;
    }

    public function setThursdayAmOpen(?\DateTimeInterface $ThursdayAmOpen): self
    {
        $this->ThursdayAmOpen = $ThursdayAmOpen;

        return $this;
    }

    public function getThursdayAmClose(): ?\DateTimeInterface
    {
        return $this->ThursdayAmClose;
    }

    public function setThursdayAmClose(?\DateTimeInterface $ThursdayAmClose): self
    {
        $this->ThursdayAmClose = $ThursdayAmClose;

        return $this;
    }

    public function getThursdayPmOpen(): ?\DateTimeInterface
    {
        return $this->ThursdayPmOpen;
    }

    public function setThursdayPmOpen(?\DateTimeInterface $ThursdayPmOpen): self
    {
        $this->ThursdayPmOpen = $ThursdayPmOpen;

        return $this;
    }

    public function getThursdayPmClose(): ?\DateTimeInterface
    {
        return $this->ThursdayPmClose;
    }

    public function setThursdayPmClose(?\DateTimeInterface $ThursdayPmClose): self
    {
        $this->ThursdayPmClose = $ThursdayPmClose;

        return $this;
    }

    public function getFridayAmOpen(): ?\DateTimeInterface
    {
        return $this->FridayAmOpen;
    }

    public function setFridayAmOpen(?\DateTimeInterface $FridayAmOpen): self
    {
        $this->FridayAmOpen = $FridayAmOpen;

        return $this;
    }

    public function getFridayAmClose(): ?\DateTimeInterface
    {
        return $this->FridayAmClose;
    }

    public function setFridayAmClose(?\DateTimeInterface $FridayAmClose): self
    {
        $this->FridayAmClose = $FridayAmClose;

        return $this;
    }

    public function getFridayPmOpen(): ?\DateTimeInterface
    {
        return $this->FridayPmOpen;
    }

    public function setFridayPmOpen(?\DateTimeInterface $FridayPmOpen): self
    {
        $this->FridayPmOpen = $FridayPmOpen;

        return $this;
    }

    public function getSaturdayAmOpen(): ?\DateTimeInterface
    {
        return $this->SaturdayAmOpen;
    }

    public function setSaturdayAmOpen(?\DateTimeInterface $SaturdayAmOpen): self
    {
        $this->SaturdayAmOpen = $SaturdayAmOpen;

        return $this;
    }

    public function getSaturdayAmClose(): ?\DateTimeInterface
    {
        return $this->SaturdayAmClose;
    }

    public function setSaturdayAmClose(?\DateTimeInterface $SaturdayAmClose): self
    {
        $this->SaturdayAmClose = $SaturdayAmClose;

        return $this;
    }

    public function getSaturdayPmOpen(): ?\DateTimeInterface
    {
        return $this->SaturdayPmOpen;
    }

    public function setSaturdayPmOpen(?\DateTimeInterface $SaturdayPmOpen): self
    {
        $this->SaturdayPmOpen = $SaturdayPmOpen;

        return $this;
    }

    public function getSaturdayPmClose(): ?\DateTimeInterface
    {
        return $this->SaturdayPmClose;
    }

    public function setSaturdayPmClose(?\DateTimeInterface $SaturdayPmClose): self
    {
        $this->SaturdayPmClose = $SaturdayPmClose;

        return $this;
    }

    public function getSundayAmOpen(): ?\DateTimeInterface
    {
        return $this->SundayAmOpen;
    }

    public function setSundayAmOpen(?\DateTimeInterface $SundayAmOpen): self
    {
        $this->SundayAmOpen = $SundayAmOpen;

        return $this;
    }

    public function getSundayAmClose(): ?\DateTimeInterface
    {
        return $this->SundayAmClose;
    }

    public function setSundayAmClose(?\DateTimeInterface $SundayAmClose): self
    {
        $this->SundayAmClose = $SundayAmClose;

        return $this;
    }

    public function getSundayPmOpen(): ?\DateTimeInterface
    {
        return $this->SundayPmOpen;
    }

    public function setSundayPmOpen(?\DateTimeInterface $SundayPmOpen): self
    {
        $this->SundayPmOpen = $SundayPmOpen;

        return $this;
    }

    public function getSundayPmClose(): ?\DateTimeInterface
    {
        return $this->SundayPmClose;
    }

    public function setSundayPmClose(?\DateTimeInterface $SundayPmClose): self
    {
        $this->SundayPmClose = $SundayPmClose;

        return $this;
    }

    public function getFridayPmClose(): ?\DateTimeInterface
    {
        return $this->FridayPmClose;
    }

    public function setFridayPmClose(?\DateTimeInterface $FridayPmClose): self
    {
        $this->FridayPmClose = $FridayPmClose;

        return $this;
    }
}
