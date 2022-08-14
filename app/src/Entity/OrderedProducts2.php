<?php

namespace App\Entity;

use App\Repository\OrderedProducts2Repository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderedProducts2Repository::class)]
class OrderedProducts2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderedProducts2s')]
    private ?Order2 $orderNumber = null;

    #[ORM\ManyToOne(inversedBy: 'orderedProducts2s')]
    private ?Product2 $product = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $number = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?Order2
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?Order2 $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }


}
