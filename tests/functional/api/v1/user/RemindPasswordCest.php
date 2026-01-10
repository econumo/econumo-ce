<?php

declare(strict_types=1);

namespace App\Tests\functional\api\v1\user;

use App\EconumoBundle\Domain\Events\RemindPasswordRequestedEvent;
use App\Tests\FunctionalTester;
use Codeception\Util\HttpCode;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class RemindPasswordCest
{
    private string $url = '/api/v1/user/remind-password';

    public function requestShouldProduceEvent(FunctionalTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'john@econumo.test']);
        $I->seeResponseCodeIs(HttpCode::OK);

        /** @var ReceiverInterface $transport */
        $transport = $I->getContainerService('messenger.transport.domain_events');
        $I->assertCount(1, $transport->get());
        $event = $transport->get()[0]->getMessage();
        $I->assertTrue($event instanceof RemindPasswordRequestedEvent);
    }
}
