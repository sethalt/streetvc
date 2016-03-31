<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/23/2014
 * Time: 6:07 PM
 */

namespace StreetVC\BancboxInvest\BancboxInvestBundle\Tests\Traits;

use StreetVC\TransactionBundle\Document\BankAccount;

class BancboxAccountTraitTest extends \PHPUnit_Framework_TestCase {

    /**
     * @throws \Exception
     * @expectedException \Exception
     */
    public function testGetAccountByInvalidIdThrowsException()
    {
        $accountTrait = $this->getObject();
        $accountTrait->getAccountById(new \MongoId());
    }

    public function testAddFunds()
    {
        $business = $this->getObject();
        $amount = 5;
        $business->addFunds($amount);
        $this->assertEquals($business->getBalance(), $amount);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithdrawFunds()
    {
        $business = $this->getObject();
        $this->assertEquals($business->getBalance(), 0);
        $amount = 5;
        $business->withdrawFunds($amount);
    }

    /**
     * @expectedException \Exception
     */
    public function testCannotAddNegativeFunds()
    {
        $business = $this->getObject();
        $amount = -5;
        $business->addFunds($amount);
        $this->assertEquals($business->getBalance(), 0);
    }

    /**
     */
    public function testGetAccountById()
    {
        $business = $this->getObject();

        $account1 = new BankAccount();
        $id = $account1->getId();
        $account2 = new BankAccount();
        $business->addLinkedAccount($account1);
        $business->addLinkedAccount($account2);

        $result = $business->getAccountById($id);
        $this->assertEquals($id, $result->getId());
    }

    public function getObject()
    {
        $accountTrait = $this->getObjectForTrait('StreetVC\BancboxInvest\BancboxInvestBundle\Traits\BancboxAccountTrait');
        return $accountTrait;
    }
}
