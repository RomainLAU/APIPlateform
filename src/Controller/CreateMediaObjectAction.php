<?php
 
namespace App\Controller;
 
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateMediaObjectAction extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function __invoke(Request $request): Media
    {
        $uploadedFile = $request->files->get('file');
 
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
 
        $mediaObject = new Media();
        $mediaObject->setImageFile($uploadedFile);
 
        return $mediaObject;
    }
}