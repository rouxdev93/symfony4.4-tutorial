<?php


namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Loading user language from DB and keeping in session
 *
 * Class UserLocalSubscriber
 * @package App\Event
 */
class UserLocalSubscriber implements EventSubscriberInterface
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * UserLocalSubscriber constructor.
     * @param SessionInterface $session
     */
    public function __construct(
        SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [ 'onInteractiveLogin', 15 ]
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->session->set('_locale', $user->getPreferences()->getLocale());
    }
}