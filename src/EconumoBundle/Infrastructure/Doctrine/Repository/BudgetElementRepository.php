<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\Folder;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\BudgetElementRepositoryInterface;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\DeleteTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\GetEntityReferenceTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\NextIdentityTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\SaveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BudgetElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetElement[]    findAll()
 * @method BudgetElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetElementRepository extends ServiceEntityRepository implements BudgetElementRepositoryInterface
{
    use NextIdentityTrait;
    use SaveTrait;
    use DeleteTrait;
    use GetEntityReferenceTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetElement::class);
    }

    public function getByBudgetId(Id $budgetId): array
    {
        return $this->findBy(
            [
                'budget' => $this->getEntityManager()->getReference(Budget::class, $budgetId)
            ],
            ['position' => 'ASC']
        );
    }

    public function getReference(Id $id): BudgetElement
    {
        return $this->getEntityReference(BudgetElement::class, $id);
    }

    public function get(Id $budgetId, Id $externalElementId): BudgetElement
    {
        $item = $this->findOneBy(
            [
                'budget' => $this->getEntityReference(Budget::class, $budgetId),
                'externalId' => $externalElementId
            ]
        );
        if (!$item instanceof BudgetElement) {
            throw new NotFoundException(
                sprintf(
                    'BudgetElementOption with ID %s not found',
                    $externalElementId->getValue()
                )
            );
        }

        return $item;
    }

    public function getNextPosition(Id $budgetId, ?Id $folderId): int
    {
        $builder = $this->createQueryBuilder('e');
        $builder
            ->select('e')
            ->where('e.budget = :budget')
            ->setParameter('budget', $this->getEntityReference(Budget::class, $budgetId))
            ->orderBy('e.position', 'DESC')
            ->setMaxResults(1);
        if ($folderId instanceof Id) {
            $builder
                ->andWhere('e.folder = :folder')
                ->setParameter('folder', $this->getEntityReference(Folder::class, $folderId));
        }

        try {
            $element = $builder->getQuery()->getSingleResult();
            $position = $element->getPosition() + 1;
        } catch (NoResultException) {
            $position = 0;
        }

        return $position;
    }

    public function getElementsByExternalId(Id $externalElementId): array
    {
        return $this->findBy(['externalId' => $externalElementId]);
    }

    public function deleteByBudgetId(Id $budgetId): void
    {
        $this->createQueryBuilder('e')
            ->delete()
            ->where('e.budget = :budget')
            ->setParameter('budget', $this->getEntityReference(Budget::class, $budgetId))
            ->getQuery()
            ->execute();
    }
}
