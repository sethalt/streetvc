<?php
/**
 * User: dao
 * Date: 7/29/14
 * Time: 11:38 AM
 */

namespace StreetVC\UserBundle\Event;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UpdateSecurityRolesListener
 * @package StreetVC\UserBundle\Event
 * @DI\Service("streetvc.user.roles.listener")
 * @DI\DoctrineMongoDBListener(
 *   events = {"preUpdate"}
 * )
 */
class UpdateSecurityRolesListener {

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @param SecurityContextInterface $security
     * @DI\InjectParams
     */
    public function __construct(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $token = $this->security->getToken();

        if (!$this->guard($args)) {
            return;
        }

        $newRoles = $args->getNewValue('roles');
        $newToken = new UsernamePasswordToken($token->getUser(), $token->getCredentials(), 'main', $newRoles);
        $this->security->setToken($newToken);
    }

    private function guard(PreUpdateEventArgs $args)
    {
        $token = $this->security->getToken();
        $object = $args->getDocument();

        if (!$object instanceof UserInterface) {
            return false;
        }

        if (!$token->getUser() == $object) {
            return false;
        }

        if (!$args->hasChangedField('roles')) {
            return false;
        }
        return true;
    }

}
