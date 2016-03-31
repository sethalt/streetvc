<?php

namespace StreetVC\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use StreetVC\UserBundle\Document\User;

class UserEvent extends Event
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return StreetVC\UserBundle\Document\User
     */
    public function getUser()
    {
        return $this->user;
    }

}