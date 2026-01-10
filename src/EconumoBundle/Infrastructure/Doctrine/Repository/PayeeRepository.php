<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Payee;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @method Payee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payee[]    findAll()
 * @method Payee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayeeRepository extends ServiceEntityRepository implements PayeeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payee::class);
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

        return $this->createQueryBuilder('c')
            ->andWhere('c.user IN(:users)')
            ->setParameter('users', $users)
            ->orderBy('c.position', Criteria::ASC)
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

    public function get(Id $id): Payee
    {
        $item = $this->find($id);
        if (!$item instanceof Payee) {
            throw new NotFoundException(sprintf('Payee with ID %s not found', $id));
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function save(array $payees): void
    {
        try {
            foreach ($payees as $payee) {
                $this->getEntityManager()->persist($payee);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException | ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getReference(Id $id): Payee
    {
        return $this->getEntityManager()->getReference(Payee::class, $id);
    }

    public function delete(Payee $payee): void
    {
        $this->getEntityManager()->remove($payee);
        $this->getEntityManager()->flush();
    }
}
