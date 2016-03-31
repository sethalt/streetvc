<?php
/**
 * Created by PhpStorm.
 * User: dao
 * Date: 7/25/14
 * Time: 4:49 PM
 */

namespace StreetVC\BorrowerBundle\Event;


use StreetVC\BorrowerBundle\Document\Business;
use Symfony\Component\EventDispatcher\Event;

class BusinessEvent extends Event {

    private $business;
    private $user;

    public function __construct(Business $business)
    {
        $this->business = $business;
        $this->user = $this->business->getUser();
    }

    public function getBusiness()
    {
        return $this->business;
    }

    public function getUser()
    {
        return $this->user;
    }
}
