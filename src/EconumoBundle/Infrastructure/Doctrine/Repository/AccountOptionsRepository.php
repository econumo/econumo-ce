<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\AccountOptions;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\AccountOptionsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method AccountOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountOptions[]    findAll()
 * @method AccountOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountOptionsRepository extends ServiceEntityRepository implements AccountOptionsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountOptions::class);
    }

    public function getByUserId(Id $userId): array
    {
        $builder = $this->createQueryBuilder('ao');
        return $builder
            ->where('ao.user = :user')
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId))
            ->orderBy('ao.position', Criteria::ASC)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function save(array $accountOptions): void
    {
        try {
            foreach ($accountOptions as $position) {
                $this->getEntityManager()->persist($position);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException|ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(Id $accountId, Id $userId): AccountOptions
    {
        $item = $this->findOneBy([
            'account' => $this->getEntityManager()->getReference(Account::class, $accountId),
            'user' => $this->getEntityManager()->getReference(User::class, $userId)
        ]);
        if (!$item instanceof AccountOptions) {
            throw new NotFoundException(
                sprintf('AccountOptions for account_id %s user_id %s not found', $accountId, $userId)
            );
        }

        return $item;
    }

    public function delete(AccountOptions $options): void
    {
        $this->getEntityManager()->remove($options);
        $this->getEntityManager()->flush();
    }
}
