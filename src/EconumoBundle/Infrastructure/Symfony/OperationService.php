<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Symfony;

use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Infrastructure\Doctrine\Factory\OperationIdFactory;
use App\EconumoBundle\Infrastructure\Doctrine\Repository\OperationIdRepository;
use App\EconumoBundle\Infrastructure\Exception\OperationObjectLockedException;
use App\EconumoBundle\UI\Service\Dto\OperationDto;
use App\EconumoBundle\UI\Service\OperationServiceInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class OperationService implements OperationServiceInterface
{
    private readonly LockFactory $lockFactory;

    public function __construct(private readonly OperationIdRepository $operationIdRepository, private readonly OperationIdFactory $operationIdFactory)
    {
        $this->lockFactory = new LockFactory(new FlockStore());
    }

    /**
     * @throws OperationObjectLockedException
     */
    public function checkIfHandled(Id $id): bool
    {
        try {
            $registeredId = $this->operationIdRepository->get($id);
            if ($registeredId->isHandled()) {
                return true;
            }
        } catch (NotFoundException) {
        }

        $lock = $this->lockFactory->createLock($id->getValue());
        if ($lock->isAcquired()) {
            throw new OperationObjectLockedException();
        }

        return false;
    }

    public function lock(Id $id): OperationDto
    {
        $lock = $this->lockFactory->createLock($id->getValue(), 10);
        if ($lock->acquire()) {
            try {
                $this->operationIdRepository->get($id);
            } catch (NotFoundException) {
                $operationId = $this->operationIdFactory->create($id);
                $this->operationIdRepository->save([$operationId]);
            }

            $dto = new OperationDto();
            $dto->operationId = $id;
            $dto->lock = $lock;
            return $dto;
        }

        throw new ValidationException('Operation id locked');
    }

    public function release(OperationDto $dto): void
    {
        $dto->lock->release();
        $dto->lock = null;

        $saved = $this->operationIdRepository->get($dto->operationId);
        $saved->markHandled();

        $this->operationIdRepository->save([$saved]);
    }
}
