<?php

namespace StreetVC\LoanBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use StreetVC\LoanBundle\Document\Escrow;

class EscrowEvent extends Event
{

    private $escrow;

    public function __construct(Escrow $escrow)
    {
        $this->escrow = $escrow;
    }

    /**
     *
     * @return \StreetVC\LoanBundle\Document\Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }
}