<?php
//namespace StreetVC\BancboxInvest\BancboxInvestBundle\Tests\Adapter;

use GuzzleHttp\Command\Model;
use StreetVC\UserBundle\Document\User;
use StreetVC\BancboxInvest\BancboxInvestBundle\Adapter\CreateInvestorAdapter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use StreetVC\BorrowerBundle\Document\Business;

class CreateIssuerAdapterTest extends \PHPUnit_Framework_TestCase
{

    protected $user;
    protected $business;
    protected $model;
    protected $accessor;

    protected function setUp()
    {
        $this->successResponse = '{ "request_id": 666296084698, "api_status":1, "id": 846267297614, "account_number": "540126101552", "account_routing_number": "053112505", "account_type": "CHECKING" }';
        $this->errorResponse = '{ "request_id": 100994911337, "error": { "type": "E-P-003", "message": "dob must be of the following format : yyyy-MM-dd" }, "api_status": 0 }';
        $this->user = $this->provideUser();
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function provideUser()
    {
        $user = new User();
        $user->setFirstName('test')->setLastName('test')->setDateOfBirth(new \DateTime('1980-01-01'))->setSocialSecurityNumber('999-99-9999');
        $user->getAddress()->setState('WI');
        $user->setBusiness(new Business());
        $user->getBusiness()->setTaxId('99999999');
        return $user;
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
        $model = new Model(json_decode($this->successResponse, 1));
        CreateInvestorAdapter::fromResponse($this->user, $model);

        foreach ($mapping as $from => $to) {
            $this->assertSame($model[$from], $this->accessor->getValue($this->user, $to));
        }
    }

    /*
    public function testErrorResponse() {
        $mapping = CreateInvestorAdapter::getResponseMapping();
        $model = new Model(json_decode($this->errorResponse, 1));
        print_r($model);
        CreateInvestorAdapter::fromResponse($this->user, $model);
    }
    */
}