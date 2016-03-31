<?php
namespace StreetVC\BancboxInvest\BancboxInvestBundle\Adapter;

use StreetVC\UserBundle\Document\User;
use Symfony\Component\PropertyAccess\PropertyAccess;
use GuzzleHttp\Command\Model;

class CreateIssuerAdapter
{

    public function __construct($requestMapping = array(), $responseMapping = array())
    {
        $this->requestMapping = $requestMapping ?: self::getRequestMapping();
        $this->responseMapping = $responseMapping ?: self::getResponseMapping();
    }

    public static function getRequestMapping()
    {
        $mapping = [
//            'reference_id' => 'business.id',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'dob' => 'dob',
            'ssn' => 'social_security_number',
            'email' => 'email',
            'phone' => 'business.phone',
            'address_1' => 'business.address.address',
            'address_2' => 'business.address.address2',
            'city' => 'business.address.city',
            'state' => 'business.address.state',
            'zip' => 'business.address.zip',
            'company_name' => 'business.legal_name',
            'company_tax_id' => 'business.tax_id',
            'company_registration_state' => 'business.registration_state',
        ];
        return $mapping;
    }

    public static function getResponseMapping()
    {
        $mapping = [
            'id' => 'business.bancbox_id',
            'bank_name' => 'business.internal_account.name',
            'account_type' => 'business.internal_account.type',
            'account_number' => 'business.internal_account.account_number',
            'account_routing_number' => 'business.internal_account.routing_number',
//            'linked_external_bank_account.id' => 'business.linked_accounts[0].bancbox_id',
//            'linked_external_bank_account.linked_bank_account_reference_id' => 'business.linked_accounts[0].bancbox_id',
//            'linked_external_bank_account.bank_account_number' => 'business.linked_accounts[0].account_number',
//            'linked_external_bank_account.bank_account_routing' => 'business.linked_accounts[0].routing_number',
//            'linked_external_bank_account.bank_account_type' => 'business.linked_accounts[0].type',
//            'linked_external_bank_account.bank_account_holder' => 'business.linked_accounts[0].account_holder',
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
        $data = [];
        foreach ($mapping as $from=>$to) {
            $value = $accessor->getValue($response, "[$from]");
            $accessor->setValue($user, $to, $value);
        }
        return $user;
    }
}
