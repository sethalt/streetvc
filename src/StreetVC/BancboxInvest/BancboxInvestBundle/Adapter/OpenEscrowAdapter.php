<?php
namespace StreetVC\BancboxInvest\BancboxInvestbundle\Adapter;

use StreetVC\UserBundle\Document\User;
use Symfony\Component\PropertyAccess\PropertyAccess;
use GuzzleHttp\Command\Model;
use StreetVC\LoanBundle\Document\LoanRequest;

class OpenEscrowAdapter
{

    public function __construct($requestMapping = array(), $responseMapping = array())
    {
        $this->requestMapping = $requestMapping ?: self::getRequestMapping();
        $this->responseMapping = $responseMapping ?: self::getResponseMapping();
    }

    public static function getRequestMapping()
    {
        $mapping = [
            'reference_id' => 'id',
            'issuer_id' => 'business.bancbox_id',
            'project_name' => 'title',
            'start_date' => 'start_date',
            'close_date' => 'close_date',
            'funding_goal' => 'funding_goal',
            'minimum_funding_amount' => 'minimum_funding_amount',
            'maximum_funding_amount' => 'maximum_funding_amount',
            'over_funding_amount' => 'overfunding_amount',
        ];
        return $mapping;
    }

    public static function getResponseMapping()
    {
        $mapping = [
            'id' => 'bancbox_id',
            'bank_name' => 'lender.internal_account.name',
            'account_type' => 'lender.internal_account.type',
            'account_number' => 'lender.internal_account.account_number',
            'account_routing_number' => 'lender.internal_account.routing_number',
        ];
        return $mapping;
    }

    public static function toRequest(LoanRequest $object)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mapping = self::getRequestMapping();
        $data = [];
        foreach ($mapping as $to=>$from) {
            $data[$to] = $accessor->getValue($object, $from);
        }
        $data['securities_offered'] = 'DEBT';
        return $data;
    }

    public static function fromResponse(LoanRequest $object, Model $response)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mapping = self::getResponseMapping();
        $data = [];
        foreach ($mapping as $from=>$to) {
            $value = $accessor->getValue($response, "[$from]");
            $accessor->setValue($object, $to, $value);
        }
        return $object;
    }
}
