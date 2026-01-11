<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use DateTimeImmutable;
use DateTime;
use App\EconumoBundle\Domain\Entity\CurrencyRate;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\CurrencyRateRepositoryInterface;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @method CurrencyRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRate[]    findAll()
 * @method CurrencyRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRateRepository extends ServiceEntityRepository implements CurrencyRateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    /**
     * @inheritDoc
     */
    public function getAll(?DateTimeInterface $date = null): array
    {
        if (!$date instanceof DateTimeInterface) {
            $dateBuilder = $this->createQueryBuilder('cr')
                ->select('cr.publishedAt')
                ->setMaxResults(1)
                ->orderBy('cr.publishedAt', Criteria::DESC);
        } else {
            $dateBuilder = $this->createQueryBuilder('cr')
                ->select('cr.publishedAt')
                ->setMaxResults(1)
                ->orderBy('cr.publishedAt', Criteria::DESC)
                ->where('cr.publishedAt <= :date')
                ->setParameter('date', $date);
        }

        $lastDate = $dateBuilder->getQuery()->getSingleScalarResult();
        $ratesDate = DateTime::createFromFormat('Y-m-d', $lastDate);

        $query = $this->createQueryBuilder('cr')
            ->andWhere('cr.publishedAt = :date')
            ->setParameter('date', $ratesDate, Types::DATE_MUTABLE)
            ->getQuery();

        return $query->getResult();
    }

    public function get(Id $currencyId, Id $baseCurrencyId, DateTimeInterface $date): CurrencyRate
    {
        $sql =<<<'SQL'
SELECT cr.id FROM currencies_rates cr
WHERE cr.currency_id = :currency
AND cr.base_currency_id = :baseCurrency
AND cr.published_at = :date
ORDER BY cr.published_at DESC
LIMIT 1
SQL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'currency' => $currencyId->getValue(),
            'baseCurrency' => $baseCurrencyId->getValue(),
            'date' => $date->format('Y-m-d')
        ]);
        $id = $result->fetchOne();
        if ($id === false) {
            throw new NotFoundException(sprintf('Currency with identifier (%s, %s) not found', $currencyId->getValue(), $date->format('Y-m-d')));
        }

        $item = $this->find(new Id($id));
        if (!$item instanceof CurrencyRate) {
            throw new NotFoundException(sprintf('Currency with identifier (%s, %s) not found', $currencyId->getValue(), $date->format('Y-m-d')));
        }

        return $item;
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

    public function getNextIdentity(): Id
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $uuid = Uuid::uuid4();

        return new Id($uuid->toString());
    }

    public function getAverage(DateTimeInterface $startDate, DateTimeInterface $endDate, Id $baseCurrencyId): array
    {
        $sql =<<<'SQL'
SELECT cr.currency_id as "currencyId", AVG(cr.rate) as rate
FROM currencies_rates cr
WHERE cr.published_at >= :startDate AND cr.published_at < :endDate
AND cr.base_currency_id = :baseCurrency
GROUP BY cr.currency_id, cr.base_currency_id
SQL;

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'baseCurrency' => $baseCurrencyId->getValue()
        ]);

        return $result->fetchAllAssociative();
    }

    public function getLatestDate(Id $baseCurrencyId, ?DateTimeInterface $date = null): DateTimeInterface
    {
        $sql =<<<'SQL'
SELECT cr.published_at FROM currencies_rates cr
WHERE cr.base_currency_id = :baseCurrency
AND cr.published_at < :date
ORDER BY cr.published_at DESC
LIMIT 1
SQL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'baseCurrency' => $baseCurrencyId->getValue(),
            'date' => $date->format('Y-m-d')
        ]);
        /** @var string|false $date */
        $date = $result->fetchOne();
        if ($date === false) {
            throw new NotFoundException(sprintf('Not found currency rates for the base currency with identifier %s', $baseCurrencyId->getValue()));
        }

        return new DateTimeImmutable($date);
    }
}
