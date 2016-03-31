<?php
use StreetVC\BancboxInvest\Client\BancboxInvestClient;
use GuzzleHttp\Client;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use StreetVC\BancboxInvest\BancboxInvestbundle\Adapter\CreateInvestorAdapter;
use StreetVC\UserBundle\Document\User;

class CreateInvestorTest extends \PHPUnit_Framework_TestCase
{

    protected $client;

    protected $mockSuccessJson;

    protected function setUp()
    {
        $this->client = new Client();
        $json = '{"api_status":1,"id":891617029249,"account_number":"542745126116","account_routing_number":"053112505","account_type":"CHECKING","account_holder_name":"Dylan Oliver"}';

        $mock = $this->getMockSuccess($json);
        $this->client->getEmitter()->attach($mock);
    }

    /*
    public function testCreateInvestorRequest()
    {
        $config = [
            'api_key' => 'test',
            'secret' => 'test',
            'created_by' => 'test'

        ];
        $client = new BancboxInvestClient($this->client, $config);
        $investor = array(
            'first_name' => 'dylan',
            'last_name' => 'oliver',
            'email' => 'dylan.oliver@gmail.com',
            'phone' => '1234567890',
            'dob' => '1975-02-28',
            'ssn' => '112-22-3333',
            'address_1' => '228 E Jefferson St',
            'city' => 'ATLANTA',
            'state' => 'WI',
            'zip' => '53588',
            'created_by' => 'Dylan'
        );
        $response = $client->createInvestor($investor);
        $user = new User();
        CreateInvestorAdapter::fromResponse($user, $response);

        $this->assertSame($response['api_status'], 1);
        $this->assertSame($user->getLender()
            ->getInternalAccount()
            ->getAccountNumber(), $response['account_number']);
        $this->assertSame($user->getLender()
            ->getInternalAccount()
            ->getRoutingNumber(), $response['account_routing_number']);
    }
    */

    public function testGetInvestorRequest()
    {
        $json = '{"request_id":265234470113,"api_status":1,"status":"ACTIVE","id":891617029249,"reference_id":"53b484f26803fa20258b4567","cip_status":"IGNORED","first_name":"Dylan","last_name":"Oliver","email":"dylan.oliver@streetvc.com","phone":"815-985-1490","dob":"1981-05-13","ssn":"999-99-9999","funds":0,"pending_balance":0,"address":{"address_1":"228 E Jefferson St","city":"Spring Green","state":"WI","zip":"53588"},"type":"STANDARD","audit_info":{"created_on":"2014-07-02","created_by":"53b484f26803fa20258b4567","modified_on":"2014-07-02","modified_by":"53b484f26803fa20258b4567"},"account_info":{"bank_name":"Four Oaks Bank \u0026 Trust Company","account_number":"542745126116","account_routing_number":"053112505","account_type":"CHECKING","account_holder_name":"Dylan Oliver"},"linked_external_accounts":[],"internal":"0","international":"0"}';
    }

    protected function getMockSuccess($json)
    {
        return new Mock([new Response(200, [ 'Content-Type' => 'application/json' ], Stream::factory($json))]);
    }
}