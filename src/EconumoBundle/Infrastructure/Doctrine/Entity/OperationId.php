<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Doctrine\Repository\OperationIdRepository")
 * @ORM\Table(name="`operation_requests_ids`")
 */
class OperationId
{
    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private bool $isHandled = false;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $updatedAt;

    public function __construct(/**
     * @ORM\Id()
     * @ORM\CustomIdGenerator("NONE")
     * @ORM\Column(type="uuid")
     */
    private Id $id, DateTimeInterface $createdAt)
    {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function markHandled(): void
    {
        $this->isHandled = true;
        $this->updatedAt = new DateTime();
    }

    public function isHandled(): bool
    {
        return $this->isHandled;
    }
}
