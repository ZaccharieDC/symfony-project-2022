<?php

namespace App\EventSubscriber;

use App\Entity\Advert;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Workflow\Event\Event;

class AdvertWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly NotifierInterface $notifier)
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.advert_publishing.gard.publish' => 'advertPublished',
            'workflow.advert_publishing.transition.publish' => 'notifyAuthor'
        ];
    }

    public function notifyAuthor(Event $event)
    {
        $advert = $event->getSubject();

        if (!$advert instanceof Advert) {
            return;
        }

        $notification = new Notification();
        $notification->subject('Advert published')->content('Your advert has been published');

        $recipient = new Recipient($advert->getEmail());

        $this->notifier->send($notification, $recipient);
    }
}