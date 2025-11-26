<?php

namespace App\Entity;

use App\Repository\CvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CvRepository::class)]
class Cv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numberOfView = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberOfView(): ?int
    {
        return $this->numberOfView;
    }

    public function setNumberOfView(int $numberOfView): static
    {
        $this->numberOfView = $numberOfView;

        return $this;
    }
}
