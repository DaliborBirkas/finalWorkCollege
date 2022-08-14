<?php

namespace App\Entity;

use App\Repository\Favorite2Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Favorite2Repository::class)]
class Favorite2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Product2 $product = null;

    #[ORM\Column]
    private ?int $likes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product2
    {
        return $this->product;
    }

    public function setProduct(?Product2 $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }
}
