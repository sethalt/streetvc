<?php
/**
 * User: dao
 * Date: 7/25/14
 * Time: 4:48 PM
 */

namespace StreetVC\BorrowerBundle\Event\Listener;

use JMS\DiExtraBundle\Annotation as DI;
use StreetVC\BorrowerBundle\Event\BusinessEvent;

/**
 * Class BusinessCreatedListener
 * @package StreetVC\BorrowerBundle\Event\Listener
 * @DI\Service
 */
class BusinessCreatedListener {

    /**
     * @DI\Observe("business.created")
     * @param BusinessEvent $event
     */
    public function onBusinessCreated(BusinessEvent $event)
    {
        $business = $event->getBusiness();
        $user = $event->getUser();
        $user->addRole("ROLE_ISSUER");
    }
}
