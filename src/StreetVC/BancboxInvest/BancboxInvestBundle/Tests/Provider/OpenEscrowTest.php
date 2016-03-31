<?php

use StreetVC\Tests\BaseTestCase;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LoanBundle\Model\LoanRequestManager;
use StreetVC\BancboxInvest\BancboxInvestBundle\Provider\BancboxProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use StreetVC\BancboxInvest\Client\BancboxInvestClient;
use StreetVC\UserBundle\Document\User;
use StreetVC\BorrowerBundle\Document\Business;

/**
 * Class OpenEscrowTest
 */
class OpenEscrowTest extends BaseTestCase
{
    private $hclient;
    private $client;
    private $config;
    /**
     * @var BancboxProvider
     */
    private $provider;

    public function setUp()
    {
        parent::setUp();
        $this->client = new Client();
        $this->config = ['api_key'=>'test', 'secret'=>'test', 'created_by' => 'test' ];
        $json = '{ "request_id": 176769982999, "status": "OPEN PENDING", "api_status": 1, "id": 333148411741, "reference_id": "personnn_ref", "project_name": "HJP1", "current_balance": 0.00, "total_funding": 0.00, "platform_signatory_name": "personerrortest sharma", "platform_signatory_title": "Crowdfunding Portal", "platform_signatory_email": "person+error_test@bancbox.com", "issuer_id": 70068738657, "issuer_reference_id": "personrrtr", "issuer_signatory_name": "JOHN SMITH", "issuer_signatory_title": "Issuer", "issuer_signatory_email": "person+iss@bancbox.com", "minimum": 1000.00, "maximum": 2000.00, "start_date": "2014-07-10", "close_date": "2014-07-30", "securities_offered": "EQUITY", "contract_reference_id": "contractDofRefIds", "initiated_on": "2014-02-12", "initiated_by": "Jiah", "modified_on": "2014-02-12", "modified_by": "Jiah" }';
        $mock = new Mock([
        new Response(200, ['Content-Type' => 'application/json'], Stream::factory($json))
        ]);
        $this->client->getEmitter()->attach($mock);
        $this->hclient = new BancboxInvestClient($this->client, $this->config);
        $this->provider = $this->getProvider();
    }

    public function getLoanRequest()
    {
        $user = new User();
        $business = $user->getOrCreateBusiness();
        $business->setBancboxId('6037275103');
        $lr = $business->createLoanRequest();
        $lr->setTitle('New Widget Factory');
        $lr->setFundingGoal(20000);
        $lr->setStartDate(new \DateTime('+1 day'));
        $lr->setCloseDate(new \DateTime('+12 months'));
        $lr->setTerm(12);
//        $lr->setIssuerId('6037275103');
        $escrow = $lr->createEscrow();
//        $lr->setEscrow($escrow);
        return $lr;
    }

    public function testLoanRequestIsMapped()
    {
        $lr = $this->getLoanRequest();
        $response = $this->provider->openEscrow($lr);
        $this->assertEquals($response['api_status'], 1);
    }

    /**
     * @return BancboxProvider
     */
    public function getProvider()
    {
        $provider = $this->get('bancbox_provider');
        return $provider;
    }

    protected function getMockSuccessClient()
    {
        $client = new Client();
        $json = [ 'api_status' => 1 , 'id' => 1 ];
        $mock = new Mock([
        new Response(200, ['Content-Type' => 'application/json'], Stream::factory(json_encode($json)))
        ]);
        $client->getEmitter()->attach($mock);
        return $client;
    }

}
