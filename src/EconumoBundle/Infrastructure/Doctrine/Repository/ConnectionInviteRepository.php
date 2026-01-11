<?php
declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\ConnectionInvite;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\ConnectionCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\ConnectionInviteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method ConnectionInvite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConnectionInvite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConnectionInvite[]    findAll()
 * @method ConnectionInvite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectionInviteRepository extends ServiceEntityRepository implements ConnectionInviteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConnectionInvite::class);
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

    public function getByUser(Id $userId): ?ConnectionInvite
    {
        return $this->findOneBy([
            'user' => $this->getEntityManager()->getReference(User::class, $userId)
        ]);
    }

    public function delete(ConnectionInvite $item): void
    {
        $item->clearCode();
        $this->getEntityManager()->flush();
    }

    public function getByCode(ConnectionCode $code): ConnectionInvite
    {
        $item = $this->findOneBy(['code' => $code]);
        if (!$item instanceof ConnectionInvite) {
            throw new NotFoundException(sprintf('ConnectionCode with CODE %s not found', $code));
        }

        if ($item->isExpired()) {
            $this->delete($item);
            throw new NotFoundException(sprintf('ConnectionCode with CODE %s not found', $code));
        }

        return $item;
    }
}
