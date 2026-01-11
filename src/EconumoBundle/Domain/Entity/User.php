<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\ReportPeriod;
use App\EconumoBundle\Domain\Events\UserRegisteredEvent;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use App\EconumoBundle\Domain\Traits\EventTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EntityTrait;
    use EventTrait;

    /**
     * @var string The hashed password
     */
    private string $password;

    private bool $isActive = true;

    /**
     * @var Collection|self[]
     */
    private Collection $connections;

    /**
     * @var Collection|UserOption[]
     */
    private Collection $options;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Id $id,
        private string $identifier,
        private string $salt,
        private string $email,
        private string $name,
        private string $avatarUrl,
        DateTimeInterface $createdAt
    ) {
        $this->connections = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->registerEvent(new UserRegisteredEvent($id));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function updateEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function updatePassword(string $password): void
    {
        $this->password = $password;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function updateUserIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function updateAvatarUrl(string $url): void
    {
        $this->avatarUrl = $url;
    }

    public function isUserConnected(self $user): bool
    {
        return $this->connections->contains($user);
    }

    public function isUserIdConnected(Id $userId): bool
    {
        foreach ($this->connections as $connection) {
            if ($connection->getId()->isEqual($userId)) {
                return true;
            }
        }

        return false;
    }

    public function connectUser(self $user): void
    {
        if ($user->getId()->isEqual($this->getId())) {
            return;
        }

        if (!$user->isActive()) {
            return;
        }

        $this->connections->add($user);
    }

    public function deleteConnection(self $user): void
    {
        $this->connections->removeElement($user);
    }

    /**
     * @return self[]|Collection
     */
    public function getConnections(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isActive', true))
            ->orderBy(['name' => Criteria::ASC]);
        return $this->connections->matching($criteria);
    }

    public function createOption(UserOption $option): UserOption
    {
        foreach ($this->options as $item) {
            if ($item->getName() === $option->getName()) {
                $this->options->removeElement($item);
            }
        }

        $this->options->add($option);
        return $option;
    }

    public function getOption(string $name): ?UserOption
    {
        foreach ($this->options as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }

        return null;
    }

    public function deleteOption(string $name): void
    {
        foreach ($this->options as $item) {
            if ($item->getName() === $name) {
                $this->options->removeElement($item);
            }
        }
    }

    /**
     * @return UserOption[]|Collection
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function getCurrency(): CurrencyCode
    {
        foreach ($this->options as $option) {
            if ($option->getName() === UserOption::CURRENCY) {
                return new CurrencyCode($option->getValue());
            }
        }

        return new CurrencyCode(UserOption::DEFAULT_CURRENCY);
    }

    public function getDefaultBudgetId(): ?Id
    {
        foreach ($this->options as $option) {
            if ($option->getName() === UserOption::BUDGET) {
                if (!$option->getValue()) {
                    return null;
                }

                return new Id($option->getValue());
            }
        }

        return null;
    }

    public function updateCurrency(CurrencyCode $currencyCode): void
    {
        foreach ($this->options as $option) {
            if ($option->getName() === UserOption::CURRENCY) {
                $option->updateValue($currencyCode->getValue());
            }
        }
    }

    public function updateReportPeriod(ReportPeriod $reportPeriod): void
    {
        foreach ($this->options as $option) {
            if ($option->getName() === UserOption::CURRENCY) {
                $option->updateValue($reportPeriod->getValue());
            }
        }
    }

    public function updateDefaultBudget(?Id $budgetId): void
    {
        foreach ($this->options as $option) {
            if ($option->getName() === UserOption::BUDGET) {
                if (!$budgetId instanceof Id) {
                    $option->updateValue(null);
                } else {
                    $option->updateValue($budgetId->getValue());
                }

                return;
            }
        }
    }

    public function completeOnboarding(): void
    {
        foreach ($this->options as $option) {
            if ($option->getName() === UserOption::ONBOARDING) {
                $option->updateValue(UserOption::ONBOARDING_VALUE_COMPLETED);
            }
        }
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function deactivate(): void
    {
        if ($this->isActive()) {
            $this->isActive = false;
            $this->updatedAt = new DateTime();
        }
    }

    public function activate(): void
    {
        if (!$this->isActive()) {
            $this->isActive = true;
            $this->updatedAt = new DateTime();
        }
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
