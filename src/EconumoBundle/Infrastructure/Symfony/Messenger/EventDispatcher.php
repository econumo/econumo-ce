<?php

declare(strict_types=1);


namespace App\EconumoBundle\Infrastructure\Symfony\Messenger;

use App\EconumoBundle\Domain\Service\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus, private readonly LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        try {
            return $this->dispatchEvent($event);
        } catch (Throwable $throwable) {
            $this->logger->error(sprintf('Error dispatch event %s', $event::class));
            throw $throwable;
        }
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    /**
     * @inheritDoc
     */
    public function dispatchDelayed(object $event, int $delay): void
    {
        $this->messageBus->dispatch($event, [new DelayStamp($delay * 1000)]);
    }

    protected function dispatchEvent(object $event)
    {
        $envelope = $this->messageBus->dispatch($event);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if ($handledStamps === []) {
            return null;
        }

        return $handledStamps[0]->getResult();
    }
}
