<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\OpenApi\Model;
use App\Controller\CreateCommandeAction;
use App\Controller\GetFilteredCommandeAction;
use App\Controller\UpdateCommandeAction;
use App\State\GetFilteredCommandesProcessor;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"
        // controller: GetFilteredCommandeAction::class
    ),
        new Get(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"),
        new Post(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_WAITER')",
        controller: CreateCommandeAction::class,
        deserialize: false,
        validationContext: ['groups' => ['Default', 'write']],
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'table_number' => [
                                    'type' => 'integer',
                                    'format' => 'binary'
                                ],
                                'ordered_drinks' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'integer',
                                        'format' => 'binary'
                                    ]

                                ],
                            ]
                        ]
                    ]
                ])
            )
        )),
    
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"),
        new Patch(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')",
        routeName: 'commandes_update',
        controller: UpdateCommandeAction::class,
        deserialize: false,
        validationContext: ['groups' => ['Default', 'write']],
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'status' => [
                                    'type' => 'integer',
                                    'format' => 'binary'
                                ],
                                'ordered_drinks' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'integer',
                                        'format' => 'binary'
                                    ]

                                ],
                            ]
                        ]
                    ]
                ])
            )
        ),
    ),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS')"),
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['created_at'])]
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $table_number = null;

    /**
     * @var Collection<int, Boisson>
     */
    #[ORM\ManyToMany(targetEntity: Boisson::class)]
    #[Groups(['read', 'write'])]
    private Collection $ordered_drinks;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?User $waiter = null;

    #[ORM\ManyToOne]
    #[Groups(['read', 'write'])]
    private ?User $barman = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Status $status = null;

    public function __construct()
    {
        $this->ordered_drinks = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getTableNumber(): ?int
    {
        return $this->table_number;
    }

    public function setTableNumber(int $table_number): static
    {
        $this->table_number = $table_number;

        return $this;
    }

    /**
     * @return Collection<int, Boisson>
     */
    public function getOrderedDrinks(): Collection
    {
        return $this->ordered_drinks;
    }

    public function addOrderedDrink(Boisson $orderedDrink): static
    {
        if (!$this->ordered_drinks->contains($orderedDrink)) {
            $this->ordered_drinks[] = $orderedDrink;
        }

        return $this;
    }

    public function setOrderedDrinks(array $orderedDrinks): static
    {
        $this->ordered_drinks = new ArrayCollection($orderedDrinks);

        return $this;
    }

    public function removeOrderedDrink(Boisson $orderedDrink): static
    {
        $this->ordered_drinks->removeElement($orderedDrink);

        return $this;
    }

    public function getWaiter(): ?User
    {
        return $this->waiter;
    }

    public function setWaiter(?User $waiter): static
    {
        $this->waiter = $waiter;

        return $this;
    }

    public function getBarman(): ?User
    {
        return $this->barman;
    }

    public function setBarman(?User $barman): static
    {
        $this->barman = $barman;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }
}
