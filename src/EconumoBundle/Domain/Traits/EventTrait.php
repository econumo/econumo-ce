<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Traits;

trait EventTrait
{
    /**
     * @var object[]
     */
    private array $events = [];

    protected function registerEvent(object $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @internal Domain usage only
     * @return object[]
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    /**
     * @internal Domain usage only
     * @return object[]
     */
    public function getReleaseEvents(): array
    {
        return $this->events;
    }
}
