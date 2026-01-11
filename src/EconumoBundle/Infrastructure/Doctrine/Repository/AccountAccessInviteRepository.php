<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\AccountAccessInvite;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\AccountAccessInviteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method AccountAccessInvite|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountAccessInvite|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountAccessInvite[]    findAll()
 * @method AccountAccessInvite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountAccessInviteRepository extends ServiceEntityRepository implements AccountAccessInviteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountAccessInvite::class);
    }

    /**
     * @inheritDoc
     */
    public function save(array $items): void
    {
        try {
            foreach ($items as $item) {
                $this->getEntityManager()->persist($item);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException | ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function get(Id $accountId, Id $recipientId): AccountAccessInvite
    {
        $item = $this->findOneBy([
            'account' => $this->getEntityManager()->getReference(Account::class, $accountId),
            'recipient' => $this->getEntityManager()->getReference(User::class, $recipientId)
        ]);
        if (!$item instanceof AccountAccessInvite) {
            throw new NotFoundException('AccountAccessInvite not found');
        }

        return $item;
    }

    public function delete(AccountAccessInvite $invite): void
    {
        $this->getEntityManager()->remove($invite);
        $this->getEntityManager()->flush();
    }

    public function getByUserAndCode(Id $userId, string $code): AccountAccessInvite
    {
        $item = $this->findOneBy(
            [
                'recipient' => $this->getEntityManager()->getReference(User::class, $userId),
                'code' => $code
            ]
        );
        if (!$item instanceof AccountAccessInvite) {
            throw new NotFoundException('AccountAccessInvite not found');
        }

        return $item;
    }

    public function getUnacceptedByUser(Id $userId): array
    {
        return $this->findBy(['owner' => $this->getEntityManager()->getReference(User::class, $userId)]);
    }
}
