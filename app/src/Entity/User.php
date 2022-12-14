<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints as Assert;




#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
     denormalizationContext: ['groups'=>'user:write'],
    normalizationContext: ['groups'=>'user:read'],
)]
#[UniqueEntity(
    fields: ['email','pib']
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['users_showAll'])]
    private ?int $id = null;

    #[ORM\Column(type:'string',length: 180, unique: true)]
    #[Groups(['users_showAll','user:write','user:read'])]
    #[Email]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['users_showAll','user:write','user:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['users_showAll','user:write'])]
    #[NotBlank]
    private ?string $password = null;

    #[Groups(['users_showAll','user:write','user:read'])]
    #[ORM\Column(length: 255)]
    #[NotBlank]
    #[Type('string')]
    private ?string $name = null;

    #[Groups(['users_showAll','user:write','user:read'])]
    #[ORM\Column(length: 255)]
    #[NotBlank]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['users_showAll','user:write','user:read'])]
    #[NotBlank]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['users_showAll','user:write','user:read'])]
    #[NotBlank]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['users_showAll','user:write','user:read'])]
    #[NotBlank]
    private ?string $address = null;

    #[ORM\Column(type: 'integer', unique: true)]
    #[Groups(['users_showAll','user:write','user:read'])]
    #[NotBlank]

    private ?int $pib = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[NotBlank]
    #[Groups(['users_showAll','user:write','user:read'])]
    #[ApiSubresource]
    #[Valid]
    private ?City $city = null;

    #[ORM\Column]
    private ?bool $is_verified = null;

    #[ORM\Column]
    private ?bool $isEmailVerified = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateOfCreation = null;

    #[ORM\Column]
    private ?int $verificationExpire = null;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Debt $debt = null;

    #[ORM\Column]
    private ?int $rabat = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Logs::class)]
    private Collection $logs;



    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FavoriteProduct::class)]
    private Collection $favoriteProducts;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->favoriteProducts = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPib(): ?int
    {
        return $this->pib;
    }

    public function setPib(int $pib): self
    {
        $this->pib = $pib;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function isIsEmailVerified(): ?bool
    {
        return $this->isEmailVerified;
    }

    public function setIsEmailVerified(bool $isEmailVerified): self
    {
        $this->isEmailVerified = $isEmailVerified;

        return $this;
    }



    public function getDateOfCreation(): ?\DateTimeInterface
    {
        return $this->dateOfCreation;
    }

    public function setDateOfCreation(\DateTimeInterface $dateOfCreation): self
    {
        $this->dateOfCreation = $dateOfCreation;

        return $this;
    }

    public function getVerificationExpire(): ?int
    {
        return $this->verificationExpire;
    }

    public function setVerificationExpire(int $verificationExpire): self
    {
        $this->verificationExpire = $verificationExpire;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUserId($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUserId() === $this) {
                $order->setUserId(null);
            }
        }

        return $this;
    }

    public function getDebt(): ?Debt
    {
        return $this->debt;
    }

    public function setDebt(Debt $debt): self
    {
        // set the owning side of the relation if necessary
        if ($debt->getUser() !== $this) {
            $debt->setUser($this);
        }

        $this->debt = $debt;

        return $this;
    }

    public function getRabat(): ?int
    {
        return $this->rabat;
    }

    public function setRabat(int $rabat): self
    {
        $this->rabat = $rabat;

        return $this;
    }

    /**
     * @return Collection<int, Logs>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Logs $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Logs $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, FavoriteProduct>
     */
    public function getFavoriteProducts(): Collection
    {
        return $this->favoriteProducts;
    }

    public function addFavoriteProduct(FavoriteProduct $favoriteProduct): self
    {
        if (!$this->favoriteProducts->contains($favoriteProduct)) {
            $this->favoriteProducts->add($favoriteProduct);
            $favoriteProduct->setUser($this);
        }

        return $this;
    }

    public function removeFavoriteProduct(FavoriteProduct $favoriteProduct): self
    {
        if ($this->favoriteProducts->removeElement($favoriteProduct)) {
            // set the owning side to null (unless already changed)
            if ($favoriteProduct->getUser() === $this) {
                $favoriteProduct->setUser(null);
            }
        }

        return $this;
    }






}
