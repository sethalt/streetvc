<?php


use StreetVC\LoanBundle\Document\LoanRequest;
class LoanRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoanRequest
     */
    protected $loanRequest;

    public function setUp()
    {
        $this->loanRequest = $this->getLoanRequest();
    }

    protected function getLoanRequest()
    {
        $lr = new LoanRequest();
        $lr->setStartDate(new \DateTime());
        $lr->setCloseDate(new \DateTime());
        $lr->setTitle('test request');
        $lr->setFundingGoal(500);
        return $lr;
    }

    public function testIsEditableAsDraft()
    {
        $this->loanRequest->setFiniteState('draft');
        $this->assertEquals($this->loanRequest->isEditable(), true);
    }
    public function testIsNotEditableIfNotDraft()
    {
        $this->loanRequest->setFiniteState('submitted');
        $this->assertFalse($this->loanRequest->isEditable());
    }

    public function testCreateEscrow()
    {
        $loanRequest = $this->loanRequest;
        $escrow = $loanRequest->createEscrow();
        $this->assertEquals($escrow->getId(), $loanRequest->getEscrowId());
    }

    public function testLoanRequestReflectsEscrowState()
    {
        $loanRequest = $this->loanRequest;
        $escrow = $loanRequest->createEscrow();
        $escrow->setFiniteState('test');
        $this->assertEquals($escrow->getFiniteState(), 'test');
        $this->assertEquals($escrow->getFiniteState(), $loanRequest->getEscrowState());
    }
}
