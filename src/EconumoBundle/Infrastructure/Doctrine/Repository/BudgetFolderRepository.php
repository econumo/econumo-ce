<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\BudgetFolderRepositoryInterface;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\DeleteTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\GetEntityReferenceTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\NextIdentityTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\SaveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BudgetFolder|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetFolder|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetFolder[]    findAll()
 * @method BudgetFolder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetFolderRepository extends ServiceEntityRepository implements BudgetFolderRepositoryInterface
{
    use NextIdentityTrait;
    use SaveTrait;
    use DeleteTrait;
    use GetEntityReferenceTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetFolder::class);
    }

    public function getReference(Id $id): BudgetFolder
    {
        return $this->getEntityManager()->getReference(BudgetFolder::class, $id);
    }

    public function getByBudgetId(Id $budgetId): array
    {
        return $this->findBy(
            [
                'budget' => $this->getEntityReference(Budget::class, $budgetId)
            ],
            [
                'position' => 'ASC'
            ]
        );
    }

    public function get(Id $id): BudgetFolder
    {
        $item = $this->find($id);
        if (!$item instanceof BudgetFolder) {
            throw new NotFoundException(sprintf('BudgetFolder with ID %s not found', $id));
        }

        return $item;
    }

    public function deleteByBudgetId(Id $budgetId): void
    {
        $this->createQueryBuilder('f')
            ->delete()
            ->where('f.budget = :budget')
            ->setParameter('budget', $this->getEntityReference(Budget::class, $budgetId))
            ->getQuery()
            ->execute();
    }
}
