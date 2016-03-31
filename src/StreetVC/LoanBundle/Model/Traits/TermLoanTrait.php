<?php
namespace StreetVC\LoanBundle\Model\Traits;

trait TermLoanTrait
{

    /**
     * @MongoDB\Int
     * @JMS\Expose
     * @Assert\GreaterThan(0)
     * @JMS\Expose()
     * term of loan in months
     */
    protected $term;

    /**
     * @MongoDB\Float
     * @JMS\Expose()
     * @var float
     * interest rate
     */
    protected $interest_rate;

    /**
     * @MongoDB\Float
     * @JMS\Expose()
     * @var float
     * monthly payment
     */
    protected $term_payment;

    public function setTermPayment($payment)
    {
        $this->term_payment = $payment;
    }

    public function getTermPayment()
    {
        return $this->term_payment;
    }

    public function getTotalRepayment()
    {
        return $this->getTerm() * $this->getTermPayment();
    }

    public function setTerm($term)
    {
        $this->term = (int) $term;
        return $this;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function getInterestRate()
    {
        return $this->interest_rate;
    }

    public function setInterestRate($rate)
    {
        $this->interest_rate = $rate;
        return $this;
    }

}
