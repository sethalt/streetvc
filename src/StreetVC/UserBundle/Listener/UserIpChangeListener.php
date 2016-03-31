<?php
namespace StreetVC\UserBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\Common\Persistence\ObjectManager;


/**
 * @DI\Service("streetvc.user.listener.ip_change", public=true)
 * @author dao
 *
 */
class UserIpChangeListener
{

    /**
     * @var SecurityContextInterface
     */
    private $security_context;

    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @DI\InjectParams
     * @param SecurityContextInterface $security
     * @param ObjectManager $om
     */
    public function __construct(SecurityContextInterface $security, ObjectManager $om)
    {
        $this->security_context = $security;
        $this->om = $om;
    }

    /**
     * @DI\Observe("kernel.request")
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->guard($event)) {
            return;
        }

        $ip = $event->getRequest()->getClientIp();
        $user = $this->security_context->getToken()->getUser();

        if (!$user->getLastIp() == $ip) {
            $user->updateIp($ip);
            $this->om->flush();
        }
    }

    private function guard(GetResponseEvent $event)
    {
        $security = $this->security_context;
        if (!$event->isMasterRequest()) {
            return false;
        }

        if (!$security->getToken() || !$security->isGranted("ROLE_USER")) {
            return false;
        }

        return true;
    }

}