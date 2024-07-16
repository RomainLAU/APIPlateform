<?php
 
namespace App\Controller;

use App\Entity\Boisson;
use App\Entity\Commande;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateCommandeAction extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function __invoke(Request $request): Commande
    {
      $data = json_decode($request->getContent(), true);
      $user = $this->getUser();
      $boissons = $data['ordered_drinks'];
      $tableNumber = $data['table_number'];

      if (!$boissons || !is_array($boissons)) {
          throw new BadRequestHttpException('"boissons" is required and must be an array');
      }

      $status = $this->entityManager->getRepository(Status::class)->findOneBy(['id' => 1]);

      $commande = new Commande();
      $commande->setWaiter($user);
      $commande->setTableNumber($tableNumber);
      $commande->setStatus($status);

      foreach ($boissons as $boisson) {
          $boissonEntity = $this->entityManager->getRepository(Boisson::class)->findOneBy(['id' => $boisson]);
          $commande->addOrderedDrink($boissonEntity);
      }

      return $commande;
  }
}