<?php

namespace App\Entity;

use App\Repository\TechnologyFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnologyFamilyRepository::class)]
class TechnologyFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Technology>
     */
    #[ORM\OneToMany(targetEntity: Technology::class, mappedBy: 'family')]
    private Collection $technologies;

    #[ORM\Column]
    private ?int $orderOfAppearance = null;

    public function __construct()
    {
        $this->technologies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getTechnologies(): Collection
    {
        return $this->technologies;
    }

    public function addTechnology(Technology $technology): static
    {
        if (!$this->technologies->contains($technology)) {
            $this->technologies->add($technology);
            $technology->setFamily($this);
        }

        return $this;
    }

    public function removeTechnology(Technology $technology): static
    {
        if ($this->technologies->removeElement($technology)) {
            // set the owning side to null (unless already changed)
            if ($technology->getFamily() === $this) {
                $technology->setFamily(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getOrderOfAppearance(): ?int
    {
        return $this->orderOfAppearance;
    }

    public function setOrderOfAppearance(int $orderOfAppearance): static
    {
        $this->orderOfAppearance = $orderOfAppearance;

        return $this;
    }
}
