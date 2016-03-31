<?php
namespace StreetVC\UserBundle\Manager;

use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use StreetVC\UserBundle\Event\UserEvents;
use StreetVC\UserBundle\Event\UserEvent;
use JMS\DiExtraBundle\Annotation as DI;

class UserManager extends FOSUserManager
{

    /**
     * @DI\Observe(UserEvents::INVESTOR_CREATED)
     * @param UserEvent $event
     */
    public function onInvestorCreated(UserEvent $event)
    {
        $user = $event->getUser();
        $user->addRole("ROLE_INVESTOR");
        $this->updateUser($user);
    }
}