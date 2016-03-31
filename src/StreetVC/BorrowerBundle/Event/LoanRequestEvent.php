<?php

namespace StreetVC\BorrowerBundle\Event;

use StreetVC\LoanBundle\Document\LoanRequest;
use Symfony\Component\EventDispatcher\Event;

class LoanRequestEvent extends Event
{
    private $loanRequest;
    private $result;

    public function __construct(LoanRequest $loanRequest)
    {
        $this->loanRequest = $loanRequest;
    }

    public function getLoanRequest()
    {
        return $this->loanRequest;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

}
