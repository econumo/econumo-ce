<?php
declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getNextIdentity(): Id
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $uuid = Uuid::uuid4();

        return new Id($uuid->toString());
    }

    /**
     * @inheritDoc
     */
    public function findAvailableForUserId(Id $userId): array
    {
        $dql =<<<'DQL'
SELECT IDENTITY(a.user) as user_id FROM App\EconumoBundle\Domain\Entity\AccountAccess aa
JOIN App\EconumoBundle\Domain\Entity\Account a WITH a = aa.account AND aa.user = :user
GROUP BY user_id
DQL;
        $query = $this->getEntityManager()->createQuery($dql)
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId));
        $ids = array_column($query->getScalarResult(), 'user_id');
        $ids[] = $userId->getValue();
        $users = array_map(fn($id): ?User => $this->getEntityManager()->getReference(User::class, new Id($id)), array_unique($ids));

        $builder = $this->createQueryBuilder('c')
            ->where('c.user IN(:users)')
            ->setParameter('users', $users)
            ->orderBy('c.position', Criteria::ASC);
        return $builder
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findByOwnerId(Id $userId): array
    {
        return $this->findBy(['user' => $this->getEntityManager()->getReference(User::class, $userId)]);
    }

    public function get(Id $id): Category
    {
        $item = $this->find($id);
        if (!$item instanceof Category) {
            throw new NotFoundException(sprintf('Category with ID %s not found', $id));
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function save(array $categories): void
    {
        try {
            foreach ($categories as $category) {
                $this->getEntityManager()->persist($category);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException | ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getReference(Id $id): Category
    {
        return $this->getEntityManager()->getReference(Category::class, $id);
    }

    public function delete(Category $category): void
    {
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }

    public function getByIds(array $ids): array
    {
        return $this->findBy(['id' => $ids]);
    }

    public function findByOwnersIds(array $userIds, bool $onlyActive = null): array
    {
        $users = [];
        foreach ($userIds as $userId) {
            $users[] = $this->getEntityManager()->getReference(User::class, $userId);
        }

        $builder = $this->createQueryBuilder('c');
        $builder->select('c')
            ->where($builder->expr()->in('c.user', ':users'))
            ->setParameter('users', $users);
        if ($onlyActive !== null) {
            $builder
                ->andWhere('c.isArchived = :isArchived')
                ->setParameter('isArchived', (bool) $onlyActive);
        }

        return $builder->getQuery()->getResult();
    }
}
