<?php

namespace App\Entity;

use App\Repository\InstructorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstructorRepository::class)]
class Instructor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $speciality = null;

    #[ORM\Column(type: "text")]
    private ?string $bio = null;

    #[ORM\OneToMany(mappedBy: "instructor", targetEntity: Lab::class)]
    private Collection $labs;

    #[ORM\OneToMany(mappedBy: "instructor", targetEntity: Certification::class)]
    private Collection $certifications;

    public function __construct()
    {
        $this->labs = new ArrayCollection();
        $this->certifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): static
    {
        $this->speciality = $speciality;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    /**
     * @return Collection<int, Lab>
     */
    public function getLabs(): Collection
    {
        return $this->labs;
    }

    public function addLab(Lab $lab): static
    {
        if (!$this->labs->contains($lab)) {
            $this->labs->add($lab);
            $lab->setInstructor($this);
        }

        return $this;
    }

    public function removeLab(Lab $lab): static
    {
        if ($this->labs->removeElement($lab)) {
            if ($lab->getInstructor() === $this) {
                $lab->setInstructor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setInstructor($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): static
    {
        if ($this->certifications->removeElement($certification)) {
            if ($certification->getInstructor() === $this) {
                $certification->setInstructor(null);
            }
        }

        return $this;
    }
}
