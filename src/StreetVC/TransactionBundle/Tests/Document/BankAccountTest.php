<?php
use StreetVC\TransactionBundle\Document\BankAccount;

class BankAccountTest extends \PHPUnit_Framework_TestCase
{

    protected $types;

    protected function setUp()
    {
        $this->types = [
            'CHECKING' => 'CHECKING',
            'SAVING' => 'SAVING'
        ];
        $this->bankAccount = new BankAccount();
    }

    public function testGetTypes()
    {
        $expected = $this->types;
        $this->assertEquals($expected, BankAccount::getTypes());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidType()
    {
        $bankAccount = new BankAccount();
        $bankAccount->setType('invalid');
    }

    public function testValidTypes()
    {
        foreach ($this->types as $key => $type) {
            $this->bankAccount->setType($type);
            $this->assertEquals($type, $this->bankAccount->getType());
        }
    }

    /**
     * #expectedException InvalidArgumentException
    public function testRoutingNumberAsNumber()
    {
        $this->bankAccount->setRoutingNumber(1);
        $this->bankAccount->setAccountNumber(1);
    }
     */
}