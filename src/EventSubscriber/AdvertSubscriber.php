<?php

namespace App\EventSubscriber;

use App\Event\AdvertCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class AdvertSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly NotifierInterface $notifier)
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            AdvertCreatedEvent::NAME => 'sendNotificationToAdmin'
        ];
    }

    public function sendNotificationToAdmin(AdvertCreatedEvent $event)
    {
        $notification = new Notification();
        $notification->subject('New Advert')->content('A new advert has been created');

        $recipient = new Recipient('admin@example.com');

        $this->notifier->send($notification, $recipient);
    }
}