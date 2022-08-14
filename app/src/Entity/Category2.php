<?php

namespace App\Entity;

use App\Repository\Category2Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Category2Repository::class)]
class Category2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product2::class)]
    private Collection $product2s;

    public function __construct()
    {
        $this->product2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Product2>
     */
    public function getProduct2s(): Collection
    {
        return $this->product2s;
    }

    public function addProduct2(Product2 $product2): self
    {
        if (!$this->product2s->contains($product2)) {
            $this->product2s->add($product2);
            $product2->setCategory($this);
        }

        return $this;
    }

    public function removeProduct2(Product2 $product2): self
    {
        if ($this->product2s->removeElement($product2)) {
            // set the owning side to null (unless already changed)
            if ($product2->getCategory() === $this) {
                $product2->setCategory(null);
            }
        }

        return $this;
    }
}
