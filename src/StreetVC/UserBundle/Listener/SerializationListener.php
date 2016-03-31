<?php
namespace StreetVC\UserBundle\Listener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Doctrine\ODM\MongoDB\DocumentManager;

class SerializationListener implements EventSubscriberInterface
{
    private $dm;
    private $user;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    static public function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'class' => 'StreetVC\UserBundle\Document\User',
                'method' =>'onPostSerialize'
            )
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $this->user = $event->getObject();
        $visitor = $event->getVisitor();
        /*
        $visitor->addData('pledge_max', $this->user->getMaxPledge());
        $visitor->addData('pledge_remaining', $this->user->getPledgeRemaining());
        $visitor->addData('pledge_pledged', $this->user->getPledgePledged());
        */
    }
}
