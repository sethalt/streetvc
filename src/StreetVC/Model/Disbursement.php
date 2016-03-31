<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/18/14
 * Time: 5:23 PM
 */

namespace StreetVC\Model;


use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\LoanBundle\Document\Loan;

abstract class Disbursement {

    protected $loan;

    protected $issuer;

    protected $amount;

    protected $state;

    protected $disbursed_on;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
        $this->issuer = $loan->getIssuer();

    }
    public function disburse()
    {
        $this->disbursed_on = new \DateTime();
    }

    public function getDisbursedOn()
    {
        return $this->disbursed_on;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Business
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @return Loan
     */
    public function getLoan()
    {
        return $this->loan;
    }
}
