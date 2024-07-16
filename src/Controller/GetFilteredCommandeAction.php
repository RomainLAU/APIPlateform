<?php
 
namespace App\Controller;

use App\Entity\Boisson;
use App\Entity\Commande;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class GetFilteredCommandeAction extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route(
        name: 'commandes_get',
        path: '/api/commandes',
        methods: ['GET']
    )]
    public function __invoke(): array
    {
      $user = $this->getUser();

      if (!$user) {
          throw new \Exception("Utilisateur non authentifié");
      }

      $roles = $user->getRoles();
      $statusConditions = [];

      if (in_array('ROLE_BARMAN', $roles)) {
          $statusConditions = [1];
      } else if (in_array('ROLE_WAITER', $roles)) {
          $statusConditions = [1, 2];
      }

      if (!empty($statusConditions)) {
          return $this->entityManager->getRepository(Commande::class)->findByStatus($statusConditions);
      }

      return $this->entityManager->getRepository(Commande::class)->findAll();
  }
}