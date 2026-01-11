<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits\GetEntityReferenceTrait;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository implements TransactionRepositoryInterface
{
    use GetEntityReferenceTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
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
    public function findByAccountId(Id $accountId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.account = :account')
            ->orWhere('t.accountRecipient = :account')
            ->setParameter('account', $this->getEntityManager()->getReference(Account::class, $accountId))
            ->orderBy('t.spentAt', Criteria::DESC)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function save(array $transactions): void
    {
        try {
            foreach ($transactions as $transaction) {
                $this->getEntityManager()->persist($transaction);
            }

            $this->getEntityManager()->flush();
        } catch (ORMException|ORMInvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(Id $id): Transaction
    {
        $item = $this->find($id);
        if (!$item instanceof Transaction) {
            throw new NotFoundException(sprintf('Transaction with ID %s not found', $id));
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function findAvailableForUserId(
        Id $userId,
        array $excludeAccounts = [],
        DateTimeInterface $periodStart = null,
        DateTimeInterface $periodEnd = null,
    ): array {
        $sharedAccountsQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT IDENTITY(aa.account) as accountId FROM App\EconumoBundle\Domain\Entity\AccountAccess aa WHERE aa.user = :user'
            )
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId));
        $sharedIds = array_column($sharedAccountsQuery->getScalarResult(), 'accountId');

        $accountsQuery = $this->getEntityManager()
            ->createQuery('SELECT a.id FROM App\EconumoBundle\Domain\Entity\Account a WHERE a.user = :user')
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId));
        $userAccountIds = array_column($accountsQuery->getScalarResult(), 'id');
        $accounts = array_map(
            fn(string $id): ?Account => $this->getEntityManager()->getReference(Account::class, new Id($id)),
            array_unique([...$sharedIds, ...$userAccountIds])
        );
        $filteredAccounts = [];
        foreach ($accounts as $account) {
            $found = false;
            foreach ($excludeAccounts as $accountId) {
                if ($accountId->isEqual($account->getId())) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $filteredAccounts[] = $account;
            }
        }

        $query = $this->createQueryBuilder('t')
            ->where('t.account IN(:accounts) OR t.accountRecipient IN(:accounts)')
            ->setParameter('accounts', $filteredAccounts);
        if ($periodStart && $periodEnd) {
            $query->andWhere('t.spentAt >= :periodStart')
                ->andWhere('t.spentAt < :periodEnd')
                ->setParameter('periodStart', $periodStart)
                ->setParameter('periodEnd', $periodEnd);
        }

        return $query->getQuery()->getResult();
    }

    public function delete(Transaction $transaction): void
    {
        $this->getEntityManager()->remove($transaction);
        $this->getEntityManager()->flush();
    }

    public function replaceCategory(Id $oldCategoryId, Id $newCategoryId): void
    {
        $builder = $this->createQueryBuilder('t');
        $builder->update()
            ->set('t.category', ':newCategory')
            ->setParameter('newCategory', $this->getEntityManager()->getReference(Category::class, $newCategoryId))
            ->where('t.category = :oldCategory')
            ->setParameter('oldCategory', $this->getEntityManager()->getReference(Category::class, $oldCategoryId));
        $builder->getQuery()->execute();
    }

    public function findChanges(Id $userId, DateTimeInterface $lastUpdate): array
    {
        $sharedAccountsQuery = $this->getEntityManager()
            ->createQuery(
                'SELECT IDENTITY(aa.account) as accountId FROM App\EconumoBundle\Domain\Entity\AccountAccess aa WHERE aa.user = :user'
            )
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId));
        $sharedIds = array_column($sharedAccountsQuery->getScalarResult(), 'accountId');

        $accountsQuery = $this->getEntityManager()
            ->createQuery('SELECT a.id FROM App\EconumoBundle\Domain\Entity\Account a WHERE a.user = :user')
            ->setParameter('user', $this->getEntityManager()->getReference(User::class, $userId));
        $userAccountIds = array_column($accountsQuery->getScalarResult(), 'id');
        $accounts = array_map(
            fn(string $id): ?Account => $this->getEntityManager()->getReference(Account::class, new Id($id)),
            array_unique([...$sharedIds, ...$userAccountIds])
        );

        $query = $this->createQueryBuilder('t')
            ->where('t.account IN(:accounts) OR t.accountRecipient IN(:accounts)')
            ->andWhere('t.updatedAt > :lastUpdate')
            ->setParameter('accounts', $accounts)
            ->setParameter('lastUpdate', $lastUpdate);

        return $query->getQuery()->getResult();
    }

    public function getAccountBalance(Id $accountId, DateTimeInterface $date): DecimalNumber
    {
        $dateFormatted = $date->format('Y-m-d H:i:s');
        $accountIdFormatted = $accountId->getValue();
        $sql = <<<SQL
SELECT COALESCE(incomes, 0) + COALESCE(transfer_incomes, 0) - COALESCE(expenses, 0) - COALESCE(transfer_expenses, 0) as balance
FROM (SELECT tmp.account_id, SUM(tmp.expenses) as expenses, SUM(tmp.incomes) as incomes, SUM(tmp.transfer_expenses) as transfer_expenses, SUM(tmp.transfer_incomes) as transfer_incomes
      FROM (SELECT tr1.account_id,
                   (SELECT SUM(t1.amount) FROM transactions t1 WHERE t1.account_id = tr1.account_id AND t1.type = 0 AND t1.spent_at < :date1) as expenses,
                   (SELECT SUM(t2.amount) FROM transactions t2 WHERE t2.account_id = tr1.account_id AND t2.type = 1 AND t2.spent_at < :date2) as incomes,
                   (SELECT SUM(t3.amount) FROM transactions t3 WHERE t3.account_id = tr1.account_id AND t3.type = 2 AND t3.spent_at < :date3) as transfer_expenses,
                   NULL as transfer_incomes
            FROM transactions tr1
            WHERE tr1.account_id = :account1
            GROUP BY tr1.account_id
            UNION ALL
            SELECT tr2.account_recipient_id as account_id, NULL as expenses, NULL as incomes, NULL as transfer_expenses, (SELECT SUM(t4.amount_recipient) FROM transactions t4 WHERE t4.account_recipient_id = tr2.account_recipient_id AND t4.type = 2 AND t4.spent_at < :date4) as transfer_incomes
            FROM transactions tr2
            WHERE tr2.account_recipient_id IS NOT NULL AND tr2.account_recipient_id = :account2 AND tr2.spent_at < :date5
            GROUP BY tr2.account_recipient_id) tmp
      GROUP BY tmp.account_id) bln
SQL;
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addScalarResult('balance', 'balance', 'string');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('date1', $dateFormatted);
        $query->setParameter('date2', $dateFormatted);
        $query->setParameter('date3', $dateFormatted);
        $query->setParameter('date4', $dateFormatted);
        $query->setParameter('date5', $dateFormatted);
        $query->setParameter('account1', $accountIdFormatted);
        $query->setParameter('account2', $accountIdFormatted);

        try {
            $result = $query->getSingleScalarResult();
        } catch (NoResultException) {
            $result = 0;
        }

        return new DecimalNumber($result);
    }

    public function countSpendingForCategories(
        array $categoryIds,
        array $accountsIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        if ($categoryIds === []) {
            return [];
        }

        $categoryValues = array_map(static fn(Id $id): string => $id->getValue(), $categoryIds);
        $accountValues = array_map(static fn(Id $id): string => $id->getValue(), $accountsIds);
        $startDateString = $startDate->format('Y-m-d H:i:s');
        $endDateString = $endDate->format('Y-m-d H:i:s');

        // Build parameter placeholders for IN clauses
        $categoryPlaceholders = [];
        $accountPlaceholders = [];
        foreach ($categoryValues as $i => $value) {
            $categoryPlaceholders[] = ":category{$i}";
        }
        foreach ($accountValues as $i => $value) {
            $accountPlaceholders[] = ":account{$i}";
        }

        $categoriesIn = implode(', ', $categoryPlaceholders);
        $accountsIn = implode(', ', $accountPlaceholders);

        $sql = <<<SQL
SELECT sum(t.amount) as amount, t.category_id, a.currency_id FROM transactions t
LEFT JOIN accounts a ON t.account_id = a.id AND a.id IN ({$accountsIn})
WHERE t.category_id IN ({$categoriesIn}) AND t.spent_at >= :startDate AND t.spent_at < :endDate AND t.tag_id IS NULL
GROUP BY a.currency_id, t.category_id
SQL;
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('category_id', 'category_id');
        $rsm->addScalarResult('currency_id', 'currency_id');
        $rsm->addScalarResult('amount', 'amount', 'string');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        // Bind parameters
        foreach ($categoryValues as $i => $value) {
            $query->setParameter("category{$i}", $value);
        }
        foreach ($accountValues as $i => $value) {
            $query->setParameter("account{$i}", $value);
        }
        $query->setParameter('startDate', $startDateString);
        $query->setParameter('endDate', $endDateString);

        return $query->getResult();
    }

    public function countSpendingForTags(
        array $tagsIds,
        array $accountsIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        if ($tagsIds === []) {
            return [];
        }

        $tagValues = array_map(static fn(Id $id): string => $id->getValue(), $tagsIds);
        $accountValues = array_map(static fn(Id $id): string => $id->getValue(), $accountsIds);
        $startDateString = $startDate->format('Y-m-d H:i:s');
        $endDateString = $endDate->format('Y-m-d H:i:s');

        // Build parameter placeholders for IN clauses
        $tagPlaceholders = [];
        $accountPlaceholders = [];
        foreach ($tagValues as $i => $value) {
            $tagPlaceholders[] = ":tag{$i}";
        }
        foreach ($accountValues as $i => $value) {
            $accountPlaceholders[] = ":account{$i}";
        }

        $tagsIn = implode(', ', $tagPlaceholders);
        $accountsIn = implode(', ', $accountPlaceholders);

        $sql = <<<SQL
SELECT sum(t.amount) as amount, t.tag_id, a.currency_id FROM transactions t
JOIN accounts a ON t.account_id = a.id AND t.account_id IN ({$accountsIn})
WHERE t.tag_id IN ({$tagsIn}) AND t.spent_at >= :startDate AND t.spent_at < :endDate
GROUP BY a.currency_id, t.tag_id
SQL;
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('tag_id', 'tag_id');
        $rsm->addScalarResult('currency_id', 'currency_id');
        $rsm->addScalarResult('amount', 'amount', 'string');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        // Bind parameters
        foreach ($tagValues as $i => $value) {
            $query->setParameter("tag{$i}", $value);
        }
        foreach ($accountValues as $i => $value) {
            $query->setParameter("account{$i}", $value);
        }
        $query->setParameter('startDate', $startDateString);
        $query->setParameter('endDate', $endDateString);

        return $query->getResult();
    }

    public function countSpending(
        array $categoriesIds,
        array $accountsIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        if ($categoriesIds === []) {
            return [];
        }

        $categoryValues = array_map(static fn(Id $id): string => $id->getValue(), $categoriesIds);
        $accountValues = array_map(static fn(Id $id): string => $id->getValue(), $accountsIds);
        $startDateString = $startDate->format('Y-m-d H:i:s');
        $endDateString = $endDate->format('Y-m-d H:i:s');

        // Build parameter placeholders for IN clauses
        $categoryPlaceholders = [];
        $accountPlaceholders = [];
        foreach ($categoryValues as $i => $value) {
            $categoryPlaceholders[] = ":category{$i}";
        }
        foreach ($accountValues as $i => $value) {
            $accountPlaceholders[] = ":account{$i}";
        }

        $categoriesIn = implode(', ', $categoryPlaceholders);
        $accountsIn = implode(', ', $accountPlaceholders);

        $sql = <<<SQL
SELECT sum(t.amount) as amount, t.category_id, t.tag_id, a.currency_id FROM transactions t
JOIN accounts a ON t.account_id = a.id AND t.account_id IN ({$accountsIn})
WHERE t.category_id IN ({$categoriesIn}) AND t.spent_at >= :startDate AND t.spent_at < :endDate
GROUP BY t.category_id, t.tag_id, a.currency_id
SQL;
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('category_id', 'category_id');
        $rsm->addScalarResult('tag_id', 'tag_id');
        $rsm->addScalarResult('currency_id', 'currency_id');
        $rsm->addScalarResult('amount', 'amount', 'string');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        // Bind parameters
        foreach ($categoryValues as $i => $value) {
            $query->setParameter("category{$i}", $value);
        }
        foreach ($accountValues as $i => $value) {
            $query->setParameter("account{$i}", $value);
        }
        $query->setParameter('startDate', $startDateString);
        $query->setParameter('endDate', $endDateString);

        return $query->getResult();
    }

    public function getByCategoriesIdsForAccountsIds(
        array $categoriesIds,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $accountIds
    ): array {
        if ($categoriesIds === [] || $accountIds === []) {
            throw new DomainException('Categories and accounts are required.');
        }

        $categories = [];
        foreach ($categoriesIds as $categoryId) {
            $categories[] = $this->getEntityReference(Category::class, $categoryId);
        }

        $accounts = [];
        foreach ($accountIds as $accountId) {
            $accounts[] = $this->getEntityReference(Account::class, $accountId);
        }

        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.spentAt >= :periodStart')
            ->andWhere('t.spentAt < :periodEnd')
            ->andWhere('t.account IN (:accounts)')
            ->andWhere('t.category IN (:categories)')
            ->andWhere('t.type = :type')
            ->andWhere('t.tag IS NULL')
            ->orderBy('t.spentAt', Criteria::DESC)
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('accounts', $accounts)
            ->setParameter('categories', $categories)
            ->setParameter('type', TransactionType::createFromAlias(TransactionType::EXPENSE_ALIAS))
            ->getQuery();

        return $query->getResult();
    }

    public function getByTagsIdsForAccountsIds(
        array $tagIds,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $accountIds,
        array $categoriesIds
    ): array {
        if ($tagIds === [] || $accountIds === []) {
            throw new DomainException('Tags and accounts are required.');
        }

        $tags = [];
        foreach ($tagIds as $tagId) {
            $tags[] = $this->getEntityReference(Tag::class, $tagId);
        }

        $accounts = [];
        foreach ($accountIds as $accountId) {
            $accounts[] = $this->getEntityReference(Account::class, $accountId);
        }

        $categories = [];
        foreach ($categoriesIds as $categoryId) {
            $categories[] = $this->getEntityReference(Category::class, $categoryId);
        }

        $builder = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.spentAt >= :periodStart')
            ->andWhere('t.spentAt < :periodEnd')
            ->andWhere('t.account IN (:accounts)')
            ->andWhere('t.tag IN (:tags)')
            ->andWhere('t.type = :type')
            ->orderBy('t.spentAt', Criteria::DESC)
            ->setParameter('periodStart', $periodStart)
            ->setParameter('periodEnd', $periodEnd)
            ->setParameter('accounts', $accounts)
            ->setParameter('tags', $tags)
            ->setParameter('type', TransactionType::createFromAlias(TransactionType::EXPENSE_ALIAS));
        if ($categories !== []) {
            $builder
                ->andWhere('t.category IN (:categories)')
                ->setParameter('categories', $categories);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }
}
