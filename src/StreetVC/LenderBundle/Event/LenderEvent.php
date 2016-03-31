<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/6/14
 * Time: 4:32 PM
 */

namespace StreetVC\LenderBundle\Event;

use StreetVC\LenderBundle\Document\Lender;
use Symfony\Component\EventDispatcher\Event;

class LenderEvent extends Event {

    private $lender;
    private $user;

    public function __construct(Lender $lender)
    {
        $this->lender = $lender;
        $this->user = $lender->getUser();
    }

    public function getLender()
    {
        return $this->lender;
    }
    public function getUser()
    {
        return $this->user;
    }
}

