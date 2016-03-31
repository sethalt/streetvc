<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/28/2014
 * Time: 11:14 AM
 */

namespace StreetVC\TransactionBundle\Event\Listener;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\DiExtraBundle\Annotation as DI;
use StreetVC\BancboxInvest\Event\WebhookEvent;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\UserBundle\Document\User;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class BalanceChangeListener
 * @package StreetVC\TransactionBundle\Event\Listener
 * @DI\Tag("monolog.logger", attributes = { "channel" = "bancbox" })
 * @DI\Service("bancbox.webhook.balance_change.listener")
 */
class BalanceChangeListener {

    private $om;
    private $logger;

    /**
     * @DI\InjectParams
     * @param ObjectManager $om
     * @param Logger $logger
     */
    public function __construct(ObjectManager $om, Logger $logger)
    {
        $this->om = $om;
        $this->logger = $logger;
    }

    /**
     * @param WebhookEvent $event
     * @DI\Observe("bancbox.webhook.INVESTOR_BALANCE_CHANGE")
     */
    public function onInvestorBalanceChange(WebhookEvent $event)
    {
        $params = $event->getParams();
        $investor_id = (string) $params['investor_id'];
        $updated_amount = $params['updated_amount'];
        if ($user = $this->findUserByInvestorId($investor_id)) {
            $lender = $user->getLender();
            $lender->setCurrentBalance($updated_amount);
            $this->logger->notice("Updated balance of investor $investor_id to $updated_amount.");
            $event->setResult(true);
            $this->om->flush();
        } else {
            $event->setResult(false);
            $this->logger->error('Error locating lender with investor id '.$investor_id);
        }
    }

    /**
     * @param WebhookEvent $event
     * @DI\Observe("bancbox.webhook.ISSUER_BALANCE_CHANGE")
     */
    public function onIssuerBalanceChange(WebhookEvent $event)
    {
        $params = $event->getParams();
        $issuer_id = (string) $params['issuer_id'];
        $updated_amount = $params['updated_amount'];
        if ($business = $this->findBusinessByIssuerId($issuer_id)) {
            $business->setCurrentBalance($updated_amount);
            $this->logger->notice("Updated balance of issuer $issuer_id to $updated_amount.");
            $event->setResult(true);
            $this->om->flush();
        } else {
            $event->setResult(false);
            $this->logger->error('Error locating business with issuer id '.$issuer_id);
        }
    }

    /**
     * @param $investor_id
     * @return User
     */
    public function findUserByInvestorId($investor_id)
    {
        $qb = $this->om->createQueryBuilder('StreetVCUserBundle:User')
            ->field('lender.bancbox_id')
            ->equals($investor_id);
        $user = $qb->getQuery()->getSingleResult();
        return $user;
    }

    /**
     * @param $issuer_id
     * @return Business
     */
    public function findBusinessByIssuerId($issuer_id)
    {
        $qb = $this->om->createQueryBuilder('StreetVCBorrowerBundle:Business')
            ->field('bancbox_id')
            ->equals($issuer_id);
        $business = $qb->getQuery()->getSingleResult();
        return $business;
    }
}
