<?php

declare(strict_types=1);

namespace App\Service;

use App\Message\MailingSubscribe;
use App\Value\Newsletter;
use Symfony\Component\Messenger\MessageBusInterface;

class MailingListService
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function subscribe(Newsletter $data): void
    {
        $this->messageBus->dispatch(
            new MailingSubscribe($data->email, $data->entityId),
        );
    }
}
