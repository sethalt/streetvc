<?php
namespace StreetVC\LoanBundle\Document;
use Doctrine\Common\Collections\ArrayCollection;
use StreetVC\LenderBundle\Model\LoanCommitmentInterface;
use StreetVC\LoanBundle\Model\EscrowInterface;

/**
 * Test class for Escrow.
 * Generated by PHPUnit on 2014-07-15 at 13:25:35.
 */
class EscrowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Escrow
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Escrow;
    }

    public function testIsNotInitiallyFullyFunded()
    {
        $escrow = $this->object;
        $this->assertFalse($escrow->isFullyFunded());
        $this->assertEquals(0, $escrow->getCurrentBalance());
        $this->assertEmpty($escrow->getCommitments());
    }

    public function testInitialStateIsNew()
    {
        $this->assertEquals($this->object->getState(), 'new');
    }

    /** @expectedException \InvalidArgumentException */
    public function testCannotAddBastardLoanRequest()
    {
        $mock = new MockLoanCommitment();
        $this->object->addCommitment($mock);
    }

    public function testGetValidCommitments()
    {
        $escrow = $this->object;

        $invalid = $this->getMockCommitment();
        $invalid->expects($this->once())->method('isValid')->willReturn(false);
        $escrow->addCommitment($invalid);

        $valid = $this->getMockCommitment();
        $valid->expects($this->once())->method('isValid')->willReturn(true);
        $escrow->addCommitment($valid);

        $commitments = $escrow->getValidCommitments();
        $this->assertFalse($commitments->contains($invalid));
        $this->assertTrue($commitments->contains($valid));
    }

    public function testEscrowIsConstructedWithId()
    {
        $this->assertNotNull($this->object->getId());
    }
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    private function getMockCommitment()
    {
        $stub = $this->getMockBuilder('StreetVC\LenderBundle\Document\LoanCommitment')
            ->disableOriginalConstructor()
            ->getMock();
        $stub->expects($this->any())->method('getEscrow')->willReturn($this->object);
        return $stub;
    }
}

class EscrowStub implements EscrowInterface
{
    public function __construct()
    {
        $this->id = new \MongoId();
    }
    public function getId()
    {
        return $this->id;
    }

    public function getFundingGoal()
    {
        return 5000;
    }

    public function getCommitments()
    {
        return new ArrayCollection();
    }

}
class MockLoanCommitment implements LoanCommitmentInterface
{
   public function getEscrow()
   {
       return new EscrowStub();
   }

    public function getAmount()
    {
        return 5000;
    }

    public function getId()
    {
        return new \MongoId();
    }

    public function isValid()
    {
        return false;
    }
}