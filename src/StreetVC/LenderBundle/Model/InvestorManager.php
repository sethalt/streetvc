<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/16/2014
 * Time: 10:39 AM
 */

namespace StreetVC\LenderBundle\Model;

use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\DiExtraBundle\Annotation as DI;
use StreetVC\BancboxInvest\BancboxInvestBundle\Provider\BancboxProvider;
use StreetVC\LenderBundle\Document\Lender;
use StreetVC\LenderBundle\Event\LenderEvent;
use StreetVC\LenderBundle\Event\LenderEvents;
use StreetVC\TransactionBundle\Document\BankAccount;
use StreetVC\UserBundle\Document\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class InvestorManager
 * @package StreetVC\LenderBundle\Model
 * @DI\Service("investor_manager")
 */
class InvestorManager {

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var BancboxProvider
     */
    protected $bancbox_provider;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param DocumentManager $odm
     * @param BancboxProvider $bancbox_provider
     * @param EventDispatcherInterface $dispatcher
     * @DI\InjectParams()
     */
    public function __construct(DocumentManager $odm, BancboxProvider $bancbox_provider, EventDispatcherInterface $dispatcher)
    {
        $this->dm = $odm;
        $this->bancbox_provider = $bancbox_provider;
        $this->dispatcher = $dispatcher;
    }

    public function createLender(User $user)
    {
        $lender = $user->getLender();
        try {
            // @todo strip bancbox
            // $result = $this->bancbox_provider->createInvestor($user);
            $user->addRole("ROLE_INVESTOR");
        } catch (\Exception $e) {
            throw $e;
        }
        $this->dm->persist($lender);
        $this->dm->flush();
        $this->dispatcher->dispatch(LenderEvents::CREATED, new LenderEvent($lender));
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
        $lender = $user->getLender();
        $lender->addFunds($amount);
        $account = $lender->getAccountById($account_id);

        /**
        $data['client_ip_address'] = $user->getLastIp();
        $data['submit_timestamp'] = (new \DateTime())->format('Y-m-d H:i:s');
        $data['investor_id'] = $user->getLender()->getBancboxId();
        $data['linked_bank_account_id'] = $account->getBancboxId();
        $data['amount'] = $amount;
        $data['memo'] = $memo;
        $data['reference_id'] = $data['id'] = (string) new \MongoID();
//        $data['document_text'] = base64_encode("I verify that I request the transfer of funds to StreetVC.");
//        $data['document_version'] = '0.1';
//        $data['represented_signature'] = "My signature".
        $data = array_filter($data);

//        $fundAccount = new FundAccountClickthrough($data);
        try {
            $result = $this->bancbox_provider->fundAccount($data);
            $this->dm->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        */
        $this->dm->flush();
        return true;
    }

    public function withdrawFunds(Lender $lender, BankAccount $account, $amount, $memo = '')
    {
        $user = $lender->getUser();

        $lender->withdrawFunds($amount);
        $withdrawal = [];
        $withdrawal['investor_id'] = $lender->getBancboxId();
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
            $this->dm->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function challengeDeposit(Lender $lender, BankAccount $account)
    {
        $challenge_id = $this->bancbox_provider->createChallengeDeposit($lender->getBancboxId(), 'INVESTOR', $account->getBancboxId());
        $account->setChallengeId($challenge_id);
        $this->dm->flush();
    }
}
