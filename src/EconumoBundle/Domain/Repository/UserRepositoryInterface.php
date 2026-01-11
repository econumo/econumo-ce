<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\Identifier;

interface UserRepositoryInterface
{
    public function getNextIdentity(): Id;

    public function loadByIdentifier(Identifier $identifier): User;

    public function getByEmail(Email $email): User;

    /**
     * @param User[] $users
     */
    public function save(array $users): void;

    public function get(Id $id): User;

    public function getReference(Id $id): User;

    /**
     * @return User[]
     */
    public function getAll(): array;
}
