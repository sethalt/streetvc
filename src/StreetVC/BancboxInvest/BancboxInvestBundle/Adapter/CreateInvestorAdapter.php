<?php
namespace StreetVC\BancboxInvest\BancboxInvestBundle\Adapter;

use StreetVC\UserBundle\Document\User;
use Symfony\Component\PropertyAccess\PropertyAccess;
use GuzzleHttp\Command\Model;

class CreateInvestorAdapter
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
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'phone' => 'phone_number',
            'address_1' => 'address.address',
            'address_2' => 'address.address2',
            'city' => 'address.city',
            'state' => 'address.state',
            'zip' => 'address.zip',
            'dob' => 'dob',
            'ssn' => 'social_security_number',
        ];
        return $mapping;
    }

    public static function getResponseMapping()
    {
        $mapping = [
            'id' => 'lender.bancbox_id',
            'bank_name' => 'lender.internal_account.name',
            'account_type' => 'lender.internal_account.type',
            'account_number' => 'lender.internal_account.account_number',
            'account_routing_number' => 'lender.internal_account.routing_number',
        ];
        return $mapping;
    }

    public static function toRequest(User $user)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mapping = self::getRequestMapping();
        $data = [];
        foreach ($mapping as $to=>$from) {
            $data[$to] = $accessor->getValue($user, $from);
        }
        return $data;
    }

    public static function fromResponse(User $user, Model $response)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mapping = self::getResponseMapping();
        foreach ($mapping as $from=>$to) {
            $value = $accessor->getValue($response, "[$from]");
            $accessor->setValue($user, $to, $value);
        }
        return $user;
    }
}
