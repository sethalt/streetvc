<?php
namespace StreetVC\UserBundle\Event;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;

class RegistrationConfirmedListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $request = $event->getRequest();
        $profileType = $request->request->get('profile');
        $route = $profileType == 'borrower' ? 'new_borrower_profile' : 'new_lender_profile';
        $url = $this->router->generate($route);
        $response = new RedirectResponse($url);
        return $response;
    }
}