<?php

namespace App\Entity;

use App\Repository\Order2Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Order2Repository::class)]
class Order2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'order2s')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $orderNote = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column]
    private ?bool $sent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?bool $paid = null;

    #[ORM\OneToMany(mappedBy: 'orderNumber', targetEntity: OrderedProducts2::class)]
    private Collection $orderedProducts2s;

    public function __construct()
    {
        $this->orderedProducts2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOrderNote(): ?string
    {
        return $this->orderNote;
    }

    public function setOrderNote(string $orderNote): self
    {
        $this->orderNote = $orderNote;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @return Collection<int, OrderedProducts2>
     */
    public function getOrderedProducts2s(): Collection
    {
        return $this->orderedProducts2s;
    }

    public function addOrderedProducts2(OrderedProducts2 $orderedProducts2): self
    {
        if (!$this->orderedProducts2s->contains($orderedProducts2)) {
            $this->orderedProducts2s->add($orderedProducts2);
            $orderedProducts2->setOrderNumber($this);
        }

        return $this;
    }

    public function removeOrderedProducts2(OrderedProducts2 $orderedProducts2): self
    {
        if ($this->orderedProducts2s->removeElement($orderedProducts2)) {
            // set the owning side to null (unless already changed)
            if ($orderedProducts2->getOrderNumber() === $this) {
                $orderedProducts2->setOrderNumber(null);
            }
        }

        return $this;
    }
}
