<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Payee;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\FolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\PayeeName;
use App\EconumoBundle\Domain\Entity\ValueObject\TagName;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Service\Dto\AccountDto;
use App\EconumoBundle\Domain\Service\Dto\ImportTransactionResultDto;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;
use DateTime;
use DateTimeInterface;
use League\Csv\Reader;
use Throwable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ImportTransactionService implements ImportTransactionServiceInterface
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private PayeeRepositoryInterface $payeeRepository,
        private TagRepositoryInterface $tagRepository,
        private CurrencyRepositoryInterface $currencyRepository,
        private FolderRepositoryInterface $folderRepository,
        private AccountAccessServiceInterface $accountAccessService,
        private AccountServiceInterface $accountService,
        private CategoryServiceInterface $categoryService,
        private PayeeServiceInterface $payeeService,
        private TagServiceInterface $tagService,
        private FolderServiceInterface $folderService,
        private TransactionServiceInterface $transactionService,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private string $baseCurrency
    ) {
    }

    public function importFromCsv(
        UploadedFile $file,
        array $mapping,
        Id $userId,
        array $overrides = []
    ): ImportTransactionResultDto
    {
        $result = new ImportTransactionResultDto();

        if (!$file->isValid()) {
            $this->addError($result, 'Invalid file upload');
            return $result;
        }

        $overrideAccountId = $overrides['accountId'] ?? null;
        $overrideDateStr = $overrides['date'] ?? null;

        if (empty($mapping['account']) && $overrideAccountId === null) {
            $this->addError($result, 'Mapping must include "account" and "date" fields');
            return $result;
        }

        if (empty($mapping['date']) && $overrideDateStr === null) {
            $this->addError($result, 'Mapping must include "account" and "date" fields');
            return $result;
        }

        // Validate amount mode
        $useDualAmountMode = !empty($mapping['amountInflow']) || !empty($mapping['amountOutflow']);
        if ($useDualAmountMode && (empty($mapping['amountInflow']) || empty($mapping['amountOutflow']))) {
            $this->addError(
                $result,
                'Mapping must include both "amountInflow" and "amountOutflow" fields when using dual amount mode'
            );
            return $result;
        }

        if (!$useDualAmountMode && empty($mapping['amount'])) {
            $this->addError(
                $result,
                'Mapping must include either "amount" or both "amountInflow" and "amountOutflow"'
            );
            return $result;
        }

        // Load user's accounts
        $accounts = $this->accountRepository->getAvailableForUserId($userId);

        $overrideAccount = null;
        if ($overrideAccountId !== null) {
            $overrideAccount = $this->findAccountById($accounts, $overrideAccountId);
            if (!$overrideAccount) {
                $this->addError($result, 'Account not found for provided accountId');
                return $result;
            }
        }

        $accountOwnerId = $overrideAccount?->getUserId() ?? $userId;
        $availableCategories = $this->categoryRepository->findByOwnerId($accountOwnerId);
        $availablePayees = $this->payeeRepository->findByOwnerId($accountOwnerId);
        $availableTags = $this->tagRepository->findByOwnerId($accountOwnerId);

        $overrideDate = null;
        if ($overrideDateStr !== null) {
            $overrideDate = $this->parseDate($overrideDateStr);
            if (!$overrideDate) {
                $this->addError($result, "Invalid date format '{$overrideDateStr}'");
                return $result;
            }
        }

        $overrideCategory = null;
        if (array_key_exists('categoryId', $overrides)) {
            $categoryId = $overrides['categoryId'];
            if ($categoryId !== null) {
                $overrideCategory = $this->findCategoryById($availableCategories, $categoryId);
                if (!$overrideCategory) {
                    $this->addError($result, 'Category not found for provided categoryId');
                    return $result;
                }
            }
        }

        $overridePayee = null;
        if (array_key_exists('payeeId', $overrides)) {
            $payeeId = $overrides['payeeId'];
            if ($payeeId !== null) {
                $overridePayee = $this->findPayeeById($availablePayees, $payeeId);
                if (!$overridePayee) {
                    $this->addError($result, 'Payee not found for provided payeeId');
                    return $result;
                }
            }
        }

        $overrideTag = null;
        if (array_key_exists('tagId', $overrides)) {
            $tagId = $overrides['tagId'];
            if ($tagId !== null) {
                $overrideTag = $this->findTagById($availableTags, $tagId);
                if (!$overrideTag) {
                    $this->addError($result, 'Tag not found for provided tagId');
                    return $result;
                }
            }
        }

        $overrideDescription = array_key_exists('description', $overrides)
            ? (string)$overrides['description']
            : null;

        if ($overrideAccount !== null) {
            if ($overrideCategory && !$overrideCategory->getUserId()->isEqual($accountOwnerId)) {
                $this->addError($result, 'Category does not belong to the account owner');
                return $result;
            }

            if ($overridePayee && !$overridePayee->getUserId()->isEqual($accountOwnerId)) {
                $this->addError($result, 'Payee does not belong to the account owner');
                return $result;
            }

            if ($overrideTag && !$overrideTag->getUserId()->isEqual($accountOwnerId)) {
                $this->addError($result, 'Tag does not belong to the account owner');
                return $result;
            }
        }

        // Parse CSV
        $filePath = $file->getPathname();
        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);
        } catch (Throwable $throwable) {
            $this->addError($result, 'Failed to open CSV file');
            return $result;
        }

        $header = $csv->getHeader();
        if (empty($header)) {
            $this->addError($result, 'CSV file is empty or invalid');
            return $result;
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            foreach ($csv->getRecords() as $rowIndex => $rowData) {
                $rowNumber = $rowIndex + 2;

                try {
                    $rowData = $this->normalizeRowKeys($rowData);

                    // Extract fields based on mapping
                    $account = $overrideAccount;
                    if (!$account) {
                        $accountName = $this->getFieldValue($rowData, $mapping['account'] ?? null);
                        if (empty($accountName)) {
                            $this->addError($result, 'Missing required fields (account or date)', $rowNumber);
                            $result->skipped++;
                            continue;
                        }

                        $account = $this->findOrCreateAccount($accounts, $accountName, $userId);
                    }

                    $date = $overrideDate;
                    if (!$date) {
                        $dateStr = $this->getFieldValue($rowData, $mapping['date'] ?? null);
                        if (empty($dateStr)) {
                            $this->addError($result, 'Missing required fields (account or date)', $rowNumber);
                            $result->skipped++;
                            continue;
                        }

                        $date = $this->parseDate($dateStr);
                        if (!$date) {
                            $this->addError($result, "Invalid date format '{$dateStr}'", $rowNumber);
                            $result->skipped++;
                            continue;
                        }
                    }

                    // Parse amount
                    if ($useDualAmountMode) {
                        $inflowStr = $this->getFieldValue($rowData, $mapping['amountInflow'] ?? null);
                        $outflowStr = $this->getFieldValue($rowData, $mapping['amountOutflow'] ?? null);

                        $inflow = !empty($inflowStr) ? $this->parseAmount($inflowStr) : null;
                        $outflow = !empty($outflowStr) ? $this->parseAmount($outflowStr) : null;

                        if ($inflow !== null && $outflow !== null) {
                            $this->addError($result, 'Both inflow and outflow specified', $rowNumber);
                            $result->skipped++;
                            continue;
                        }

                        if ($inflow === null && $outflow === null) {
                            $this->addError($result, 'No amount specified', $rowNumber);
                            $result->skipped++;
                            continue;
                        }

                        $amount = $inflow ?? (-1 * $outflow);
                    } else {
                        $amountStr = $this->getFieldValue($rowData, $mapping['amount'] ?? null);
                        if (empty($amountStr)) {
                            $this->addError($result, 'Missing amount', $rowNumber);
                            $result->skipped++;
                            continue;
                        }
                        $amount = $this->parseAmount($amountStr);
                    }

                    if ($amount === null) {
                        $this->addError($result, 'Invalid amount format', $rowNumber);
                        $result->skipped++;
                        continue;
                    }

                    // Parse optional fields
                    $description = $overrideDescription
                        ?? ($this->getFieldValue($rowData, $mapping['description'] ?? null) ?? '');

                    $category = $overrideCategory;
                    if (!$category) {
                        $categoryName = $this->getFieldValue($rowData, $mapping['category'] ?? null);
                        $category = $categoryName
                            ? $this->findOrCreateCategory($availableCategories, $categoryName, $accountOwnerId, $amount)
                            : null;
                    }

                    $payee = $overridePayee;
                    if (!$payee) {
                        $payeeName = $this->getFieldValue($rowData, $mapping['payee'] ?? null);
                        $payee = $payeeName ? $this->findOrCreatePayee($availablePayees, $payeeName, $accountOwnerId) : null;
                    }

                    $tag = $overrideTag;
                    if (!$tag) {
                        $tagName = $this->getFieldValue($rowData, $mapping['tag'] ?? null);
                        $tag = $tagName ? $this->findOrCreateTag($availableTags, $tagName, $accountOwnerId) : null;
                    }

                    // Create transaction
                    $transactionDto = new TransactionDto();
                    $transactionDto->userId = $userId;
                    $transactionDto->type = new TransactionType($amount >= 0 ? TransactionType::INCOME : TransactionType::EXPENSE);
                    $transactionDto->account = $account;
                    $transactionDto->accountId = $account->getId();
                    $transactionDto->amount = new DecimalNumber((string)abs($amount));
                    $transactionDto->date = $date;
                    $transactionDto->description = $description;
                    $transactionDto->category = $category;
                    $transactionDto->categoryId = $category?->getId();
                    $transactionDto->payee = $payee;
                    $transactionDto->payeeId = $payee?->getId();
                    $transactionDto->tag = $tag;
                    $transactionDto->tagId = $tag?->getId();

                    $this->transactionService->createTransaction($transactionDto);
                    $result->imported++;

                } catch (Throwable $e) {
                    $this->addError($result, $e->getMessage(), $rowNumber);
                    $result->skipped++;
                }
            }

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
        return $result;
    }

    /**
     * @param Account[] $accounts
     */
    private function findAccountById(array $accounts, string $accountId): ?Account
    {
        foreach ($accounts as $account) {
            if ($account->getId()->getValue() === $accountId) {
                return $account;
            }
        }

        return null;
    }

    /**
     * @param Category[] $categories
     */
    private function findCategoryById(array $categories, string $categoryId): ?Category
    {
        foreach ($categories as $category) {
            if ($category->getId()->getValue() === $categoryId) {
                return $category;
            }
        }

        return null;
    }

    /**
     * @param Payee[] $payees
     */
    private function findPayeeById(array $payees, string $payeeId): ?Payee
    {
        foreach ($payees as $payee) {
            if ($payee->getId()->getValue() === $payeeId) {
                return $payee;
            }
        }

        return null;
    }

    /**
     * @param Tag[] $tags
     */
    private function findTagById(array $tags, string $tagId): ?Tag
    {
        foreach ($tags as $tag) {
            if ($tag->getId()->getValue() === $tagId) {
                return $tag;
            }
        }

        return null;
    }

    private function getFieldValue(array $row, ?string $fieldName): ?string
    {
        if ($fieldName === null || !isset($row[$fieldName])) {
            return null;
        }

        $value = trim($row[$fieldName]);
        return $value === '' ? null : $value;
    }

    /**
     * @param Account[] &$accounts
     */
    private function findOrCreateAccount(array &$accounts, string $name, Id $userId): Account
    {
        // Try to find existing account
        foreach ($accounts as $account) {
            if (strcasecmp($account->getName()->getValue(), $name) === 0) {
                if ($this->accountAccessService->canAddTransaction($userId, $account->getId())) {
                    return $account;
                }
            }
        }

        // Create new account if not found
        $currency = $this->currencyRepository->getByCode(new CurrencyCode($this->baseCurrency));
        if (!$currency) {
            throw new \RuntimeException("Base currency '{$this->baseCurrency}' not found. Please configure a valid base currency.");
        }
        $currencyId = $currency->getId();

        // Get user's folders - if none exist, create a default one
        $folders = $this->folderRepository->getByUserId($userId);
        if (empty($folders)) {
            $folder = $this->folderService->create($userId, new FolderName('Imported Accounts'));
            $folderId = $folder->getId();
        } else {
            $folderId = reset($folders)->getId();
        }

        $accountDto = new AccountDto();
        $accountDto->userId = $userId;
        $accountDto->name = $name;
        $accountDto->currencyId = $currencyId;
        $accountDto->icon = 'wallet';
        $accountDto->balance = new DecimalNumber('0');
        $accountDto->folderId = $folderId;

        $account = $this->accountService->create($accountDto);
        $accounts[] = $account;

        return $account;
    }

    /**
     * @param Category[] &$categories
     */
    private function findOrCreateCategory(array &$categories, string $name, Id $userId, float $amount): Category
    {
        // Try to find existing category
        foreach ($categories as $category) {
            if (strcasecmp($category->getName()->getValue(), $name) === 0) {
                return $category;
            }
        }

        // Create new category if not found
        // Determine type based on transaction amount (income if positive, expense if negative)
        $categoryType = $amount >= 0 ? CategoryType::INCOME : CategoryType::EXPENSE;

        $category = $this->categoryService->createCategory(
            $userId,
            new CategoryName($name),
            new CategoryType($categoryType),
            new Icon('category')
        );

        $categories[] = $category;

        return $category;
    }

    /**
     * @param Payee[] &$payees
     */
    private function findOrCreatePayee(array &$payees, string $name, Id $userId): Payee
    {
        // Try to find existing payee
        foreach ($payees as $payee) {
            if (strcasecmp($payee->getName()->getValue(), $name) === 0) {
                return $payee;
            }
        }

        // Create new payee if not found
        $payee = $this->payeeService->createPayee(
            $userId,
            new PayeeName($name)
        );

        $payees[] = $payee;

        return $payee;
    }

    /**
     * @param Tag[] &$tags
     */
    private function findOrCreateTag(array &$tags, string $name, Id $userId): Tag
    {
        // Try to find existing tag
        foreach ($tags as $tag) {
            if (strcasecmp($tag->getName()->getValue(), $name) === 0) {
                return $tag;
            }
        }

        // Create new tag if not found
        $tag = $this->tagService->createTag(
            $userId,
            new TagName($name)
        );

        $tags[] = $tag;

        return $tag;
    }

    private function parseDate(string $dateStr): ?DateTimeInterface
    {
        $dateStr = trim($dateStr, " \t\n\r\0\x0B\"'");
        if ($dateStr === '') {
            return null;
        }

        if (ctype_digit($dateStr)) {
            $length = strlen($dateStr);
            if ($length === 8) {
                $ymd = DateTime::createFromFormat('!Ymd', $dateStr);
                if ($ymd !== false) {
                    $errors = DateTime::getLastErrors();
                    if ($errors !== false && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                        return $ymd;
                    }
                }
            }

            if ($length === 10 || $length === 13) {
                $timestamp = (int)$dateStr;
                if ($length === 13) {
                    $timestamp = (int)floor($timestamp / 1000);
                }

                if ($timestamp > 0) {
                    $date = new DateTime();
                    $date->setTimestamp($timestamp);
                    return $date;
                }
            }
        }

        // Try common date formats
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'Y/m/d',
            'd-m-Y',
            'm-d-Y',
            'd.m.Y',
            'm.d.Y',
            'Y.m.d',
            'Ymd',
            'd/m/y',
            'm/d/y',
            'd-m-y',
            'm-d-y',
            'd.m.y',
            'm.d.y',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'm/d/Y H:i:s',
            'm/d/Y H:i',
            'Y/m/d H:i:s',
            'Y/m/d H:i',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'm-d-Y H:i:s',
            'm-d-Y H:i',
            'd.m.Y H:i:s',
            'd.m.Y H:i',
            'm.d.Y H:i:s',
            'm.d.Y H:i',
            'Y.m.d H:i:s',
            'Y.m.d H:i',
            'Y-m-d\TH:i:s',
            'Y-m-d\TH:i',
            'Y-m-d\TH:i:sP',
            'Y-m-d\TH:i:sO',
            'Y-m-d\TH:i:s.uP',
            'Y-m-d\TH:i:s.uO',
            'Y-m-d\TH:i:s\Z',
            'Y-m-d\TH:i:s.u\Z',
            'd M Y',
            'd M Y H:i',
            'd M Y H:i:s',
            'd M Y g:i A',
            'd F Y',
            'd F Y H:i',
            'd F Y H:i:s',
            'd F Y g:i A',
            'M d Y',
            'M d, Y',
            'M d Y H:i',
            'M d, Y H:i',
            'M d Y H:i:s',
            'M d, Y H:i:s',
            'M d Y g:i A',
            'M d, Y g:i A',
            'F d Y',
            'F d, Y',
            'F d Y H:i',
            'F d, Y H:i',
            'F d Y H:i:s',
            'F d, Y H:i:s',
            'F d Y g:i A',
            'F d, Y g:i A',
            'd-M-Y',
            'd-M-Y H:i',
            'd-M-Y H:i:s',
            'd-M-Y g:i A',
            'd-F-Y',
            'd-F-Y H:i',
            'd-F-Y H:i:s',
            'd-F-Y g:i A',
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat('!' . $format, $dateStr);
            if ($date === false) {
                continue;
            }

            $errors = DateTime::getLastErrors();
            if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                continue;
            }

            return $date;
        }

        // Try strtotime as fallback
        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
            return $date;
        }

        return null;
    }

    private function parseAmount(string $amountStr): ?float
    {
        $trimmed = trim($amountStr);
        if ($trimmed === '') {
            return null;
        }

        $isNegative = str_starts_with($trimmed, '-') || (str_contains($trimmed, '(') && str_contains($trimmed, ')'));

        // Remove common currency symbols and whitespace, keep separators
        $cleaned = preg_replace('/[^\d.,]/', '', $trimmed);
        if ($cleaned === null || $cleaned === '') {
            return null;
        }

        // Handle different decimal separators
        // If there are multiple commas or dots, assume the last one is decimal separator
        $lastComma = strrpos($cleaned, ',');
        $lastDot = strrpos($cleaned, '.');

        if ($lastComma !== false && $lastDot !== false) {
            // Both present, the later one is decimal separator
            if ($lastComma > $lastDot) {
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                $cleaned = str_replace(',', '', $cleaned);
            }
        } elseif ($lastComma !== false) {
            // Only comma, check if it's decimal separator or thousands
            $commaCount = substr_count($cleaned, ',');
            if ($commaCount === 1 && strlen($cleaned) - $lastComma - 1 <= 2) {
                // Likely decimal separator
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                // Thousands separator
                $cleaned = str_replace(',', '', $cleaned);
            }
        }

        $cleaned = str_replace(',', '', $cleaned);

        if (!is_numeric($cleaned)) {
            return null;
        }

        $amount = (float)$cleaned;
        if ($isNegative) {
            $amount *= -1;
        }

        return $amount;
    }

    /**
     * @param array<string, string|null> $rowData
     * @return array<string, string|null>
     */
    private function normalizeRowKeys(array $rowData): array
    {
        $normalized = [];
        foreach ($rowData as $key => $value) {
            $normalized[$this->stripUtf8Bom((string)$key)] = $value;
        }

        return $normalized;
    }

    private function stripUtf8Bom(string $value): string
    {
        if (str_starts_with($value, "\xEF\xBB\xBF")) {
            return substr($value, 3);
        }

        return $value;
    }

    private function addError(ImportTransactionResultDto $result, string $message, ?int $rowNumber = null): void
    {
        if (!array_key_exists($message, $result->errors)) {
            $result->errors[$message] = [];
        }

        if ($rowNumber !== null) {
            $result->errors[$message][] = $rowNumber;
        }
    }
}
