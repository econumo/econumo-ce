<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process
     *
     * @return object
     *   The Event that was passed, now modified by listeners
     */
    public function dispatch(object $event);

    public function dispatchAll(array $events): void;

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process
     *
     * @param int $delay The delay in seconds
     */
    public function dispatchDelayed(object $event, int $delay): void;
}
