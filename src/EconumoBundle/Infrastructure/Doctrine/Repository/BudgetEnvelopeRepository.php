<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\BudgetEnvelope;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\BudgetEnvelopeRepositoryInterface;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\DeleteTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\GetEntityReferenceTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\NextIdentityTrait;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\SaveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Throwable;

/**
 * @method BudgetEnvelope|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetEnvelope|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetEnvelope[]    findAll()
 * @method BudgetEnvelope[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetEnvelopeRepository extends ServiceEntityRepository implements BudgetEnvelopeRepositoryInterface
{
    use NextIdentityTrait;
    use SaveTrait;
    use DeleteTrait;
    use GetEntityReferenceTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetEnvelope::class);
    }

    public function getByBudgetId(Id $budgetId, bool $onlyActive = null): array
    {
        if ($onlyActive === null) {
            return $this->findBy([
                'budget' => $this->getEntityReference(Budget::class, $budgetId)
            ]);
        } else {
            return $this->findBy([
                'budget' => $this->getEntityReference(Budget::class, $budgetId),
                'isArchived' => (bool) $onlyActive
            ]);
        }
    }

    public function deleteAssociationsWithCategories(Id $budgetId, array $categoriesIds): void
    {
        if ($categoriesIds === []) {
            return;
        }

        $conn = $this->getEntityManager()->getConnection();
        $categoriesIdsString = [];
        $placeholders = [];
        foreach ($categoriesIds as $index => $categoryId) {
            $placeholder = ':category_id_' . $index;
            $placeholders[] = $placeholder;
            $categoriesIdsString[$placeholder] = $categoryId->getValue();
        }

        $parameters = ['budget_id' => $budgetId->getValue(), ...$categoriesIdsString];
        $placeholdersString = implode(', ', $placeholders);

        $sql = <<<SQL
DELETE FROM budgets_envelopes_categories 
WHERE budget_envelope_id IN (
    SELECT id FROM budgets_envelopes WHERE budget_id = :budget_id
) AND category_id IN ({$placeholdersString})
SQL;

        try {
            $stmt = $conn->prepare($sql);
            $stmt->executeStatement($parameters);
        } catch (Throwable $throwable) {
            // Handle any database errors
            throw new RuntimeException('Database error: ' . $throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    public function get(Id $id): BudgetEnvelope
    {
        $item = $this->find($id);
        if (!$item instanceof BudgetEnvelope) {
            throw new NotFoundException(sprintf('BudgetEnvelope with ID %s not found', $id));
        }

        return $item;
    }

    public function getReference(Id $id): BudgetEnvelope
    {
        return $this->getEntityManager()->getReference(BudgetEnvelope::class, $id);
    }
}
