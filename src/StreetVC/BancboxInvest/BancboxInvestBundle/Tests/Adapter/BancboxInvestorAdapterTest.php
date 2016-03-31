<?php
//namespace StreetVC\BancboxInvest\BancboxInvestBundle\Tests\Adapter;

use GuzzleHttp\Command\Model;
use StreetVC\UserBundle\Document\User;
use StreetVC\BancboxInvest\BancboxInvestBundle\Adapter\CreateInvestorAdapter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use StreetVC\BorrowerBundle\Document\Business;

class BancboxInvestorAdapterTest extends \PHPUnit_Framework_TestCase
{

    protected $user;
    protected $model;
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $accessor;

    protected function setUp()
    {
        $this->user = new User();
        $this->user->setFirstName('test')->setLastName('test')->setDateOfBirth(new \DateTime('1980-01-01'));
        $this->user->getAddress()->setState('WI');
        $this->user->setBusiness(new Business());
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function testRequest()
    {
        $mapping = CreateInvestorAdapter::getRequestMapping();
        $data = CreateInvestorAdapter::toRequest($this->user);
        foreach ($mapping as $to=>$from) {
            $value = $this->accessor->getValue($data, "[$to]");
            $this->assertSame($value, $this->accessor->getValue($this->user, $from));
        }
    }

    public function testResponse()
    {
        $mapping = CreateInvestorAdapter::getResponseMapping();
        $model = new Model([
            'id' => 1,
            'account_number' => '999999999',
            'account_routing_number' => '999999999',
            'bank_name' => 'test bank',
            'account_type' => 'CHECKING'
        ]);
        CreateInvestorAdapter::fromResponse($this->user, $model);

        foreach ($mapping as $from => $to) {
            $this->assertSame($model[$from], $this->accessor->getValue($this->user, $to));
        }
    }
}
