<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\ConnectionCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTime;

class ConnectionInvite
{
    use EntityTrait;

    /**
     * @var string
     */
    private const INVITE_LIFETIME = '+5 minutes';

    private ?ConnectionCode $code = null;

    private ?DateTime $expiredAt = null;

    public function __construct(private User $user)
    {
    }

    public function generateNewCode(): void
    {
        $this->code = ConnectionCode::generate();
        $this->expiredAt = new DateTime(self::INVITE_LIFETIME);
    }

    public function clearCode(): void
    {
        $this->code = null;
        $this->expiredAt = null;
    }

    public function getCode(): ?ConnectionCode
    {
        return $this->code;
    }

    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
    }

    public function isExpired(): bool
    {
        return $this->expiredAt < new DateTime();
    }

    public function getUserId(): Id
    {
        return $this->user->getId();
    }
}
