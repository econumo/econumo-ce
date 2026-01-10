<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\Identifier;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Exception\UserDeactivatedException;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\EncodeServiceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EncodeServiceInterface $encodeService
    ) {
        parent::__construct($registry, User::class);
    }

    public function getNextIdentity(): Id
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $uuid = Uuid::uuid4();

        return new Id($uuid->toString());
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->updatePassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @inheritDoc
     */
    public function save(array $users): void
    {
        try {
            foreach ($users as $user) {
                $this->getEntityManager()->persist($user);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException|ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function loadByIdentifier(Identifier $identifier): User
    {
        $user = $this->findOneBy(['identifier' => $identifier->getValue()]);
        if (!$user instanceof User) {
            throw new NotFoundException(sprintf('User with identifier %s not found', $identifier));
        }

        if (!$user->isActive()) {
            throw new UserDeactivatedException('User is deactivated');
        }

        return $user;
    }

    public function get(Id $id): User
    {
        $item = $this->find($id);
        if (!$item instanceof User) {
            throw new NotFoundException(sprintf('User with ID %s not found', $id));
        }

        if (!$item->isActive()) {
            throw new UserDeactivatedException('User is deactivated');
        }

        return $item;
    }

    public function getByEmail(Email $email): User
    {
        $encodedEmail = $this->encodeService->hash($email->getValue());
        $user = $this->findOneBy(['identifier' => $encodedEmail]);
        if (!$user instanceof User) {
            throw new NotFoundException(sprintf('User with email %s not found', $email));
        }

        if (!$user->isActive()) {
            throw new UserDeactivatedException('User is deactivated');
        }

        return $user;
    }

    public function getReference(Id $id): User
    {
        return $this->getEntityManager()->getReference(User::class, $id);
    }

    public function getAll(): array
    {
        return $this->findBy(['isActive' => true]);
    }
}
