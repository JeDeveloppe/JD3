<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
class Domain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'domain')]
    private Collection $categories;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commandLineInTerminal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $renderIconStringWithoutParentheses = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setDomain($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getDomain() === $this) {
                $category->setDomain(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCommandLineInTerminal(): ?string
    {
        return $this->commandLineInTerminal;
    }

    public function setCommandLineInTerminal(?string $commandLineInTerminal): static
    {
        $this->commandLineInTerminal = $commandLineInTerminal;

        return $this;
    }

    public function getRenderIconStringWithoutParentheses(): ?string
    {
        return $this->renderIconStringWithoutParentheses;
    }

    public function setRenderIconStringWithoutParentheses(?string $renderIconStringWithoutParentheses): static
    {
        $this->renderIconStringWithoutParentheses = $renderIconStringWithoutParentheses;

        return $this;
    }
}
