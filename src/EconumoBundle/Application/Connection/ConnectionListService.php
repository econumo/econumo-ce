<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection;

use App\EconumoBundle\Application\Connection\Dto\GetConnectionListV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\GetConnectionListV1ResultDto;
use App\EconumoBundle\Application\Connection\Assembler\GetConnectionListV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Connection\ConnectionAccountServiceInterface;
use App\EconumoBundle\Domain\Service\Connection\ConnectionServiceInterface;

readonly class ConnectionListService
{
    public function __construct(
        private GetConnectionListV1ResultAssembler $getConnectionListV1ResultAssembler,
        private ConnectionServiceInterface $connectionService,
        private ConnectionAccountServiceInterface $connectionAccountService
    ) {
    }

    public function getConnectionList(
        GetConnectionListV1RequestDto $dto,
        Id $userId
    ): GetConnectionListV1ResultDto {
        $receivedAccountAccess = $this->connectionAccountService->getReceivedAccountAccess($userId);
        $issuedAccountAccess = $this->connectionAccountService->getIssuedAccountAccess($userId);
        $connectedUsers = $this->connectionService->getUserList($userId);

        return $this->getConnectionListV1ResultAssembler->assemble(
            $dto,
            $userId,
            $receivedAccountAccess,
            $issuedAccountAccess,
            $connectedUsers
        );
    }
}
