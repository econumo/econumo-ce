<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Middleware\ProtectSystemApi;

use App\EconumoBundle\UI\Middleware\ProtectSystemApi\SystemApiInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ProtectSystemApiListener implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function __construct(private string $token)
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        $controller = is_array($controller) ? $controller[0] : $controller;

        if ($controller instanceof SystemApiInterface) {
            $tokenAPI = (string)$event->getRequest()->headers->get('Authorization');

            if ($this->token === "") {
                throw new RuntimeException('ECONUMO_SYSTEM_API_KEY is not set');
            }

            if ($this->token !== $tokenAPI) {
                throw new AccessDeniedHttpException('Access denied');
            }
        }
    }
}
