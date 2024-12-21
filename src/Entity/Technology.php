<?php

namespace App\Entity;

use App\Repository\TechnologyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnologyRepository::class)]
class Technology
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $knowledgeRate = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'technologies')]
    private Collection $projects;

    #[ORM\Column(length: 255)]
    private ?string $commandLineInTerminal = null;

    #[ORM\Column(length: 255)]
    private ?string $renderIconStringWithoutParentheses = null;

    #[ORM\Column]
    private ?int $orderOfAppearance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'technologies')]
    private ?TechnologyFamily $family = null;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function getKnowledgeRate(): ?int
    {
        return $this->knowledgeRate;
    }

    public function setKnowledgeRate(int $knowledgeRate): static
    {
        $this->knowledgeRate = $knowledgeRate;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addTechnology($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeTechnology($this);
        }

        return $this;
    }

    public function getCommandLineInTerminal(): ?string
    {
        return $this->commandLineInTerminal;
    }

    public function setCommandLineInTerminal(string $commandLineInTerminal): static
    {
        $this->commandLineInTerminal = $commandLineInTerminal;

        return $this;
    }

    public function getRenderIconStringWithoutParentheses(): ?string
    {
        return $this->renderIconStringWithoutParentheses;
    }

    public function setRenderIconStringWithoutParentheses(string $renderIconStringWithoutParentheses): static
    {
        $this->renderIconStringWithoutParentheses = $renderIconStringWithoutParentheses;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFamily(): ?TechnologyFamily
    {
        return $this->family;
    }

    public function setFamily(?TechnologyFamily $family): static
    {
        $this->family = $family;

        return $this;
    }
}
