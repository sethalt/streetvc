<?php
/**
 * User: dao
 * Date: 7/25/14
 * Time: 4:48 PM
 */

namespace StreetVC\LenderBundle\Event\Listener;

use JMS\DiExtraBundle\Annotation as DI;
use StreetVC\LenderBundle\Event\LenderEvent;
use StreetVC\LenderBundle\Event\LenderEvents;

/**
 * Class LenderCreatedListener
 * @package StreetVC\LenderBundle\Event\Listener
 * @DI\Service
 */
class LenderCreatedListener {

    /**
     * @DI\Observe(LenderEvents::CREATED)
     * @param LenderEvent $event
     */
    public function onLenderCreated(LenderEvent $event)
    {
        $lender = $event->getLender();
        $user = $event->getUser();
    }
}
