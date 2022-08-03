<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
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

    #[ORM\Column(length: 255)]
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



}
