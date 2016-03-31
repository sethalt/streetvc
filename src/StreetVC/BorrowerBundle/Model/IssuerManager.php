<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/16/2014
 * Time: 10:39 AM
 */

namespace StreetVC\BorrowerBundle\Model;

use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\DiExtraBundle\Annotation as DI;
use StreetVC\BancboxInvest\BancboxInvestBundle\Model\FundAccountClickthrough;
use StreetVC\BancboxInvest\BancboxInvestBundle\Provider\BancboxProvider;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\TransactionBundle\Document\BankAccount;
use StreetVC\TransactionBundle\Document\Withdrawal;
use StreetVC\UserBundle\Document\User;

/**
 * Class InvestorManager
 * @package StreetVC\LenderBundle\Model
 * @DI\Service("issuer_manager")
 */
class IssuerManager {

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var BancboxProvider
     */
    protected $bancbox_provider;

    /**
     * @param DocumentManager $odm
     * @param BancboxProvider $bancbox_provider
     * @DI\InjectParams()
     */
    public function __construct(DocumentManager $odm, BancboxProvider $bancbox_provider)
    {
        $this->dm = $odm;
        $this->bancbox_provider = $bancbox_provider;
    }

    /**
     * @param User $user
     * @param $account_id
     * @param $amount
     * @param $memo
     * @return bool
     * @throws \Exception
     */
    public function addFunds(User $user, $account_id, $amount, $memo)
    {
        $business = $user->getBusiness();
        if (!$account = $business->getAccountById($account_id)) {
            throw new \Exception("Invalid account");
        }
        /**
        $data['client_ip_address'] = $user->getLastIp();
        $data['submit_timestamp'] = (new \DateTime())->format('Y-m-d H:i:s');
        $data['issuer_id'] = $user->getBusiness()->getBancboxId();
        $data['linked_bank_account_id'] = $account->getBancboxId();
        $data['amount'] = $amount;
        $data['memo'] = $memo;
        $data['reference_id'] = $data['id'] = (string) new \MongoID();
//        $data['document_text'] = base64_encode("I verify that I request the transfer of funds to StreetVC.");
//        $data['document_version'] = '0.1';
//        $data['represented_signature'] = "My signature".
        $data = array_filter($data);

//        $fundAccount = new FundAccountClickthrough($data);
         */
        try {
            // $result = $this->bancbox_provider->fundAccount($data);
            // $fundAccount->setPath('result', $result->toArray());
            // $balance = $result['current_balance'];
            $business->addFunds($amount);
//            $business->setCurrentBalance($balance + $amount);
//            $business->setPendingBalance($balance + $amount);
//            $this->dm->persist($fundAccount);
            $this->dm->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function withdrawFunds(Business $business, BankAccount $account, $amount, $memo = '')
    {
        $user = $business->getUser();

        /**
        $withdrawal = [];
        $withdrawal['issuer_id'] = $business->getBancboxId();
        $withdrawal['linked_bank_account_id'] = $account->getBancboxId();
        $withdrawal['amount'] = $amount;
        $withdrawal['client_ip_address'] = $user->getLastIp();
        $withdrawal['submit_timestamp'] = (new \DateTime())->format('Y-m-d H:i:s');
        $withdrawal['memo'] = $memo;
        $withdrawal['text'] = "I authorize Bancbox to make this transaction";
        $withdrawal['method'] = 'ACH';
        try {
            $result = $this->bancbox_provider->withdrawFunds($withdrawal);
//            $this->dm->persist($withdrawal);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
         */
        try {
            $business->withdrawFunds($amount, $account);
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function linkAccount(Business $business, BankAccount $account)
    {
        $business->addLinkedAccount($account);
        try {
//            $result = $this->bancbox_provider->linkIssuerAccount($business, $account);
            $this->dm->persist($account);
            $this->dm->flush();
//            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
