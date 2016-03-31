<?php

namespace StreetVC\LoanBundle\Event;

use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\UserBundle\Document\User;
use Symfony\Component\EventDispatcher\Event;
use StreetVC\LenderBundle\Document\LoanCommitment;
use Symfony\Component\Security\Core\User\UserInterface;

class FundEscrowEvent extends Event
{

    const FUND = "escrow.fund";
    const FUNDED = "escrow.funded";

    /**
     * @var LoanCommitment
     */
    private $commitment;

    /**
     * @var Escrow
     */
    private $escrow;

    /** @var UserInterface */
    private $user;

    public function __construct(LoanCommitment $commitment)
    {
        $this->commitment = $commitment;
        $this->escrow = $commitment->getEscrow();
        $this->user = $commitment->getUser();
    }

    /**
     * @return LoanCommitment
     */
    public function getCommitment()
    {
        return $this->commitment;
    }

    /**
     *
     * @return Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
