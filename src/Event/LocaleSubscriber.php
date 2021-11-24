<?php


namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LocaleSubscriber
 * @package App\Event
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $defaultLocale;

    /**
     * LocaleSubscriber constructor.
     * @param $defaultLocale
     */
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return mixed
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param mixed $defaultLocale
     */
    public function setDefaultLocale($defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::REQUEST => [ 'onKernelRequest', 20 ] //method and priority
        ];
    }


    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if(!$request->hasPreviousSession()){
            return;
        }

        if($locale = $request->attributes->get('_locale')){
            $request->getSession()->set('_locale', $locale);
        }else{
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

}

