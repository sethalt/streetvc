<?php
namespace StreetVC\BancboxInvest\BancboxInvestBundle\Provider;

use Exception;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use StreetVC\BancboxInvest\Client\BancboxInvestClient;
use StreetVC\BancboxInvest\Event\BancboxRequestEvent;
use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\TransactionBundle\Document\BankAccount;
use StreetVC\LenderBundle\Document\Lender;
use Doctrine\Common\Persistence\ObjectManager;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\UserBundle\Document\User;
use StreetVC\BancboxInvest\BancboxInvestBundle\Adapter\CreateInvestorAdapter;
use StreetVC\BancboxInvest\BancboxInvestBundle\Adapter\CreateIssuerAdapter;
use StreetVC\LoanBundle\Document\LoanRequest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use StreetVC\LenderBundle\Document\LoanCommitment;

/**
 * Class BancboxProvider
 * @package StreetVC\BancboxInvest\BancboxInvestBundle\Provider
 * #DI\Service
 */
class BancboxProvider
{
    /**
     * @var \StreetVC\BancboxInvest\Client\BancboxInvestClient
     */
    private $client;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param BancboxInvestClient $bancbox_client
     * @param ObjectManager $om
     * @param EventDispatcherInterface $dispatcher
     * @param Logger $logger bancbox logger
     * #DI\InjectParams
     */
    public function __construct(BancboxInvestClient $bancbox_client, ObjectManager $om, EventDispatcherInterface $dispatcher, Logger $logger)
    {
        $this->client = $bancbox_client;
        $this->om = $om;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $formatter = "{resource}:\n{req_body}\n{res_body}\n";
        $logSubscriber = new LogSubscriber($this->logger, $formatter);
        $httpEmitter = $this->client->getHttpClient()->getEmitter();
        $this->getBaseEmitter()->attach($logSubscriber);
    }

    /**
     * @return \GuzzleHttp\Event\EmitterInterface
     */
    protected function getBaseEmitter()
    {
        return $this->client->getHttpClient()->getEmitter();
    }

    /**
     * @return BancboxInvestClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $data
     * @return mixed
     * $response['status'] will bee SCHEDULED, FAILED, or COMPLETED
     * $response['event_id'] identifies the event
     */
    public function debitFee($data)
    {
        $response = $this->client->debitFee($data);
        return $response;
    }

    /**
     * @param $data
     * @throws Exception
     */
    public function fundAccount($data)
    {
        return $this->client->fundAccountBase($data);
    }

    /**
     * @param $investor_id
     * @return mixed
     */
    public function getInvestor($investor_id)
    {
        return $this->client->getInvestor(['investor_id' => $investor_id]);
    }

    /**
     * @param $investor_id
     * @param null $from_date
     * @param null $to_date
     * @return mixed
     */
    public function getInvestorActivity($investor_id, $from_date = null, $to_date = null)
    {
        $data = ['entity_type' => 'INVESTOR', 'entity_id' => $investor_id, 'from_date' => $from_date, 'to_date' => $to_date];
        $data = array_filter($data);
        $response = $this->client->getActivity($data)->toArray();
        return $response;
    }

    public function getIssuerActivity($issuer_id, $from_date = null, $to_date = null)
    {
        $data = ['entity_type' => 'ISSUER', 'entity_id' => $issuer_id, 'from_date' => $from_date, 'to_date' => $to_date];
        $data = array_filter($data);
        $response = $this->client->getActivity($data);
        return $response;
    }

    public function getInvestorLedger($investor_id)
    {
        $data = ['entity_type' => 'INVESTOR', 'entity_id' => $investor_id];
        $response = $this->client->getLedger($data);
        return $response;
    }

    public function getIssuerLedger($issuer_id)
    {
        $data = ['entity_type' => 'LENDER', 'entity_id' => $issuer_id];
        $response = $this->client->getLedger($data);
        return $response;
    }

    public function getEscrow($escrow_id)
    {
        $response = $this->client->getEscrow(['escrow_id' => $escrow_id]);
        return $response;
    }

    public function fundEscrow(LoanCommitment $commitment)
    {
        $map = [
            'escrow_id' => 'bancbox_escrow_id',
            'investor_id' => 'investor_id',
            'amount' => 'amount',
            'client_ip_address' => 'client_ip_address',
//            'linked_bank_account_id' => 'account.bancbox_id'
        ];
        $data = [
            'fund_on_availability' => 'N',
            'text' => "I authorize Bancbox to make this transaction",
            'submit_timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        $data = $this->map($commitment, $map, $data);
        try {
            $response = $this->client->fundEscrow($data);
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function map($object, $map, array $data)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($map as $to => $from) {
            $data[$to] = $accessor->getValue($object, $from);
        }
        array_filter($data);
        return $data;
    }

    /**
     * @param LoanRequest $request
     * @throws Exception
     */
    public function openEscrow(LoanRequest $request)
    {
        $map = [
            'name' => 'title',
            'reference_id' => 'id',
            'issuer_id' => 'issuer_id',
            'start_date' => 'start_date',
            'close_date' => 'close_date',
            'funding_goal' => 'funding_goal',
            'minimum_funding_amount' => 'minimum_funding_amount',
            'maximum_funding_amount' => 'maximum_funding_amount',
            'over_funding_amount' => 'funding_goal' // no overfunding
        ];

        $data = [
            'securities_offered' => 'DEBT',
            'platform_signatory_name' => 'dylan oliver',
            'platform_signatory_title' => 'cto',
            'platform_signatory_email' => 'dylan.oliver@streetvc.com',
            'issuer_signatory_name' => 'dylan oliver',
            'issuer_signatory_title' => 'cto',
            'issuer_signatory_email' => 'dylan.oliver@streetvc.com',
            'created_by' => 'dylan',
            'deal' => 0
        ];
        $data = $this->map($request, $map, $data);

        try {
            $response = $this->client->createEscrowAccount($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * @param Escrow $escrow
     * @throws Exception
     * @return mixed
     */
    public function disburseEscrow(Escrow $escrow)
    {
         $defaults = [
            'close_escrow_disbursal' => 'YES',
            'debit_external' => true,
            'reason' => 'GOAL_MET',
            'party_account_number[0]' => '111111111',
            'party_payment_method[0]' => 'ACH',
            'party_routing_number_ach[0]' => '053112505',
            'party_name[0]' => 'cfp',
            'party_count' => 1
        ];
        $mapping = [
            'escrow_id' => 'bancbox_id',
            'issuer_disbursal_amount' => 'issuerShare',
            'party_disbursal_amount[0]' => 'platformFee'
        ];

        $request = $this->map($escrow, $mapping, $defaults);

        try {
            $response = $this->client->disburseEscrow($request);
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

    public function createProceedsSchedules($batch) {
        try {
            $response = $this->client->createProceedsSchedules($batch);
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * @param $escrow_id
     * @param $reason
     * @return mixed
     * @throws Exception
     */
    public function cancelEscrow($escrow_id, $reason)
    {
        $data = ['escrow_id' => $escrow_id, 'reason' => $reason ];
        try {
            $response = $this->client->cancelEscrow($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

        /**
     * @param $escrow_id
     * @param $reason
     * @return mixed
     * @throws Exception
     */
    public function closeEscrow($escrow_id, $reason)
    {
        $data = ['escrow_id' => $escrow_id, 'reason' => $reason ];
        try {
            $response = $this->client->closeEscrow($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }
    /**
     * @param User $user
     * @return mixed
     * @throws Exception
     */
    public function createInvestor(User $user)
    {
        $data = CreateInvestorAdapter::toRequest($user);
        $data['created_by'] = $user->getId();
        try {
            $response = $this->client->createInvestor($data);
            $user = CreateInvestorAdapter::fromResponse($user, $response);
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

    public function createIssuer(User $user, Business $business)
    {
        try {
            $data = CreateIssuerAdapter::toRequest($user);
            $response = $this->client->createIssuer($data);
            CreateIssuerAdapter::fromResponse($user, $response);
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function linkInvestorAccount(Lender $lender, BankAccount $account)
    {
        $data['investor_id'] = $lender->getBancboxID();
        $data['linked_account_reference_id'] = $account->getId();
        $data['bank_account_number'] = $account->getAccountNumber();
        $data['bank_account_routing'] = $account->getRoutingNumber();
        $data['bank_account_type'] = $account->getType();
        $data['bank_account_holder'] = $account->getAccountHolder();
        try {
            $response = $this->client->linkExternalAccount($data);
            $linked_account = $response['linked_external_account'];
            $account->setBancboxId($linked_account['id']);
            return $linked_account;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Business $business
     * @param BankAccount $account
     * @throws Exception
     */
    public function linkIssuerAccount(Business $business, BankAccount $account)
    {
        $data['issuer_id'] = $business->getBancboxID();
        $data['linked_account_reference_id'] = $account->getId();
        $data['bank_account_number'] = $account->getAccountNumber();
        $data['bank_account_routing'] = $account->getRoutingNumber();
        $data['bank_account_type'] = $account->getType();
        $data['bank_account_holder'] = $account->getAccountHolder();
        try {
            $response = $this->client->linkExternalAccount($data);
            $linked_account = $response['linked_external_account'];
            $account->setBancboxId($linked_account['id']);
            return $linked_account;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $bancboxId
     * @param $entityType
     * @param $linkedAccountId
     * @return mixed
     * @throws Exception
     */
    public function createChallengeDeposit($bancboxId, $entityType, $linkedAccountId)
    {
        $data = [
            'entity_id' => $bancboxId,
            'entity_type' => $entityType,
            'linked_bank_account_id' => $linkedAccountId,
        ];

        try {
            $response = $this->client->createChallengeDeposit($data);
            $challenge_id = $response['challenge_id'];
            return $challenge_id;
        } catch (Exception $e) {
            // error E-L-423: "Challenge deposit present for this account" if another account is already being verified
            throw $e;
        }
    }

    public function getChallengeDepositStatus($challenge_id)
    {
        $data = ['challenge_id' => $challenge_id];
        try {
            $response = $this->client->getChallengeDepositStatus($challenge_id);
        } catch (Exception $e) {
            throw $e;
        }
        return $response['verification_process_status'];
    }

    public function verifyChallengeDeposit($challenge_id, $challenge_deposit_1, $challenge_deposit_2)
    {
        $data = [
            'challenge_id' => $challenge_id,
            'challenge_deposit_1' => $challenge_deposit_1,
            'challenge_deposit_2' => $challenge_deposit_2
        ];

        $response = $this->client->verifyChallengeDeposit($data);
        return $response;
    }

    public function withdrawFunds($withdrawal)
    {
        $response = $this->client->withdrawFunds($withdrawal);
        return $response;
    }
}
