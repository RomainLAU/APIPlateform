<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Controller\CreateMediaObjectAction;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\OpenApi\Model;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\HttpFoundation\File\File;

#[Vich\Uploadable]
#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"),
        new Get(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN') or is_granted('ROLE_WAITER')"),
        new Post(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')",
        controller: CreateMediaObjectAction::class,
        deserialize: false,
        validationContext: ['groups' => ['Default', 'write']],
        openapi: new Model\Operation(
            requestBody: new Model\RequestBody(
                content: new \ArrayObject([
                    'multipart/form-data' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'file' => [
                                    'type' => 'string',
                                    'format' => 'binary'
                                ]
                            ]
                        ]
                    ]
                ])
            )
        )),
    
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')"),
        new Patch(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS') or is_granted('ROLE_BARMAN')"),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_BOSS')"),
    ]
)]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['read', 'write'])]
    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }
}
