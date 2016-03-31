<?php

use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\LoanBundle\Document\LoanRequest;
class BusinessTest extends \PHPUnit_Framework_TestCase
{
    public function testActiveLoanRequest()
    {
        $business = $this->provideBusiness();
        $loanRequest = $business->createLoanRequest();
        $this->assertEquals($business->getActiveLoanRequest(), $loanRequest);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCannotAddLoanRequestWhileAnotherIsActive()
    {
        $business = $this->provideBusiness();
        $r1 = $business->createLoanRequest();
        $r2 = $business->createLoanRequest();
    }

    private function provideBusiness()
    {
        $user = new \StreetVC\UserBundle\Document\User();
        $business = $user->getOrCreateBusiness();
        return $business;
    }

}
