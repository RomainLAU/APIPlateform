<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BoissonRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"),
        new Get(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"),
        new Post(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')"),
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')"),
        new Patch(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')"),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')"),
    ]
)]
#[ORM\Entity(repositoryClass: BoissonRepository::class)]
class Boisson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read', 'write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?float $price = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['read', 'write'])]
    private ?Media $picture = null;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPicture(): ?Media
    {
        return $this->picture;
    }

    public function setPicture(?Media $picture): static
    {
        $this->picture = $picture;

        return $this;
    }
}