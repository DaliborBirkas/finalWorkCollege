<?php

namespace App\Entity;

use App\Repository\RandomRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RandomRepository::class)]
class Random
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $checkerValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckerValue(): ?string
    {
        return $this->checkerValue;
    }

    public function setCheckerValue(?string $checkerValue): self
    {
        $this->checkerValue = $checkerValue;

        return $this;
    }
}
