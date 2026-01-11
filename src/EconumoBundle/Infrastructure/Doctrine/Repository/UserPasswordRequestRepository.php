<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\UserPasswordRequest;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserPasswordRequestCode;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\UserPasswordRequestRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @method UserPasswordRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPasswordRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPasswordRequest[]    findAll()
 * @method UserPasswordRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPasswordRequestRepository extends ServiceEntityRepository implements UserPasswordRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPasswordRequest::class);
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

    public function getByUserAndCode(Id $userId, UserPasswordRequestCode $code): UserPasswordRequest
    {
        $item = $this->findOneBy([
            'user' => $this->getEntityManager()->getReference(User::class, $userId->getValue()),
            'code' => $code->getValue()
        ]);
        if (!$item instanceof UserPasswordRequest) {
            throw new NotFoundException(sprintf('PasswordUserRequest with ID %s not found', $userId->getValue()));
        }

        return $item;
    }

    public function removeUserCodes(Id $userId): void
    {
        $this->createQueryBuilder('upr')
            ->delete()
            ->where('upr.user = :user')
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId->getValue()))
            ->getQuery()
            ->execute();
    }

    public function delete(UserPasswordRequest $item): void
    {
        try {
            $this->getEntityManager()->remove($item);
            $this->getEntityManager()->flush();
        } catch (ORMException | ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getByUser(Id $userId): UserPasswordRequest
    {
        $item = $this->findOneBy([
            'user' => $this->getEntityManager()->getReference(User::class, $userId->getValue()),
        ]);
        if (!$item instanceof UserPasswordRequest) {
            throw new NotFoundException(sprintf('PasswordUserRequest with ID %s not found', $userId->getValue()));
        }

        return $item;
    }
}
