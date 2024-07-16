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
final class UpdateCommandeAction extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route(
        name: 'commandes_update',
        path: '/api/commandes/{commandeId}',
        methods: ['PATCH']
    )]
    public function __invoke(Request $request, int $commandeId): Response
    {
      $data = json_decode($request->getContent(), true);
      $commande = $this->entityManager->getRepository(Commande::class)->findOneById($commandeId);
      $user = $this->getUser();
      $firstStatus = $this->entityManager->getRepository(Status::class)->findOneById(1);

      if (
          (in_array('ROLE_WAITER', $user->getRoles()) ||
          in_array('ROLE_ADMIN', $user->getRoles()) ||
          in_array('ROLE_BOSS',  $user->getRoles())) &&
          isset($data['ordered_drinks']) &&
          is_array($data['ordered_drinks']) &&
          $commande->getStatus()->getId() !== 3
      ) {
          $boissons = [];

          foreach ($data['ordered_drinks'] as $boisson) {
              $boissonEntity = $this->entityManager->getRepository(Boisson::class)->findOneById($boisson);

                if ($boissonEntity) {
                    $boissons[] = $boissonEntity;
                }

              $commande->setStatus($firstStatus);
          }

            $commande->setOrderedDrinks($boissons);

          $this->entityManager->flush();

          return new Response('Commande mise à jour', 200);
      } else if (
          in_array('ROLE_BARMAN', $user->getRoles()) &&
          $commande->getBarman() === null &&
          $data['status']
      ) {
          $commande->setBarman($user);

          $status = $this->entityManager->getRepository(Status::class)->findOneById($data['status']);
          $commande->setStatus($status);

          $this->entityManager->flush();

          return new Response('Barman et status mis à jour', 200);
      } else if (
          in_array('ROLE_BARMAN', $user->getRoles()) &&
          $commande->getBarman() === null
      ) {
          $commande->setBarman($user);

          $this->entityManager->flush();

          return new Response('Barman mis à jour', 200);
      } else if (
          in_array('ROLE_BARMAN', $user->getRoles()) &&
          $commande->getBarman() !== null &&
          $commande->getBarman() === $user &&
          $data['status']
      ) {
          $status = $this->entityManager->getRepository(Status::class)->findOneById($data['status']);
          $commande->setStatus($status);

          $this->entityManager->flush();

          return new Response('Commande mise à jour', 200);
      } else if (
          (in_array('ROLE_WAITER', $user->getRoles()) ||
          in_array('ROLE_ADMIN', $user->getRoles()) ||
          in_array('ROLE_BOSS', $user->getRoles())) &&
          $commande->getStatus()->getId() === 2 &&
          $data['status']
      ) {
          $status = $this->entityManager->getRepository(Status::class)->findOneById($data['status']);
          $commande->setStatus($status);

          $this->entityManager->flush();

          return new Response('Commande mise à jour', 200);
      } else {
          return new Response('Unauthorized', 401);
      }
  }
}