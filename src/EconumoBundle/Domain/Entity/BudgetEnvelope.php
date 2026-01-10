<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetEnvelopeName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Events\BudgetEnvelopeCreatedEvent;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use App\EconumoBundle\Domain\Traits\EventTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BudgetEnvelope
{
    use EntityTrait;
    use EventTrait;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    /**
     * @var Collection|Category[]
     */
    private Collection $categories;

    private bool $isArchived = false;

    public function __construct(
        private Id $id,
        private Budget $budget,
        private BudgetEnvelopeName $name,
        private Icon $icon,
        array $categories,
        DateTimeInterface $createdAt
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->categories = new ArrayCollection();
        foreach ($categories as $category) {
            if (!$category instanceof Category) {
                throw new DomainException('Category must be instance of Category');
            }

            $this->categories->add($category);
        }

        $this->registerEvent(new BudgetEnvelopeCreatedEvent($id, $budget->getId(), $name, $icon, $createdAt));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): BudgetEnvelopeName
    {
        return $this->name;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function getIcon(): Icon
    {
        return $this->icon;
    }

    public function getBudget(): Budget
    {
        return $this->budget;
    }

    public function updateName(BudgetEnvelopeName $name): void
    {
        if (!$this->name || !$this->name->isEqual($name)) {
            $this->name = $name;
            $this->updated();
        }
    }

    public function updateIcon(Icon $icon): void
    {
        if (!$this->icon || !$this->icon->isEqual($icon)) {
            $this->icon = $icon;
            $this->updated();
        }
    }

    public function setArchive(bool $value): void
    {
        if ($this->isArchived !== $value) {
            $this->isArchived = $value;
            $this->updated();
        }
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function containsCategory(Category $category): bool
    {
        return $this->categories->contains($category);
    }

    public function addCategory(Category $category): void
    {
        if (!$this->containsCategory($category)) {
            $this->categories->add($category);
            $this->updated();
        }
    }

    public function removeCategory(Category $category): void
    {
        if ($this->containsCategory($category)) {
            $this->categories->removeElement($category);
            $this->updated();
        }
    }

    /**
     * @return Category[]|Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}