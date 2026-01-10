<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Datetime;

use DateTimeZone;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TimezoneListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $timezone = $request->headers->get('X-Timezone', 'UTC');

        // Validate the timezone
        if (!in_array($timezone, DateTimeZone::listIdentifiers())) {
            $timezone = 'UTC';
        }

        date_default_timezone_set($timezone);
    }
}
