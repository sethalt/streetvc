<?php
namespace StreetVC\BorrowerBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use StreetVC\LenderBundle\Document\LoanCommitment;

class LoanCommitmentEvent extends Event
{
    // name: street_vc.events.loan_commitment
    private $loanCommitment;

    public function getLoanCommitment()
    {
        return $this->loanCommitment;
    }

    public function setLoanCommitment(LoanCommitment $loanCommitment)
    {
        $this->loanCommitment = $loanCommitment;
    }
}