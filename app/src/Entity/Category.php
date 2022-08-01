<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Service\Attribute\Required;


#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    collectionOperations: ['get','post'],
    itemOperations: [
        'get',
        'put',
        'delete'
    ],
   #How much to return results
    #attributes: ['pagination_items_per_page'=>2]
   # denormalizationContext: ['groups'=>'category:write','swagger_definition_name'=>'Read'],
    #normalizationContext: ['groups'=>'category:read','swagger_definition_name'=>'Write'],
    #shortName: 'kategorija'

)]
##[ApiFilter(BooleanFilter::class, properties:['name'])]
##[ApiFilter(SearchFilter::class, properties: ['name'])]
##[ApiFilter(RangeFilter::class, properties: ['name'])]
##[ApiFilter(PropertyFilter::class,)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
  # #[Groups(['category:read'])]
    /**
     * Name of the category.
     * Not
     */
    #[NotBlank]
    #[Required]

    private ?string $name = null;

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
        $this->name = nl2br($name);

        return $this;
    }
}
