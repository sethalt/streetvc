<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 10/10/2014
 * Time: 12:50 PM
 */

namespace StreetVC\BancboxInvest\BancboxInvestBundle\Model;


use Doctrine\Common\Persistence\ObjectManager;
use StreetVC\BancboxInvest\BancboxInvestBundle\Provider\BancboxProvider;
use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\UserBundle\Document\User;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("bancbox.manager")
 * @author dao
 *
 */
class BancboxManager
{

    /** @var ObjectManager */
    private $om;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @var BancboxProvider
     */
    private $bancbox;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @DI\InjectParams
     * @param ObjectManager $om
     * @param EventDispatcherInterface $dispatcher
     * @param Logger $bancbox_logger
     * @param BancboxProvider $bancbox_provider
     */
    public function __construct(ObjectManager $om, EventDispatcherInterface $dispatcher, Logger $bancbox_logger, BancboxProvider $bancbox_provider)
    {
        $this->om = $om;
        $this->dispatcher = $dispatcher;
        $this->logger = $bancbox_logger;
        $this->bancbox = $bancbox_provider;
    }

    public function synchronizeLenders()
    {
        $users = $this->getRepository("StreetVCUserBundle:User")->getLenderUsers();
        $diff = [];
        /** @var User $user */
        foreach ($users as $user) {
            $id = (string)$user->getId();
            $lender = $user->getLender();
            if ($investor_id = $lender->getBancboxId()) {
                $data = $this->bancbox->getInvestor($investor_id);
                $funds = $diff[$id]['bancbox'] = $data['funds'];
                $diff[$id]['local'] = $lender->getCurrentBalance();
                if ($funds != $lender->getCurrentBalance()) {
                    $lender->setCurrentBalance($data['funds']);
                    $this->logger->notice("Updating balance of lender " . $lender->getBancboxId() . " to " . (string)$funds);
                }
            }
        }
        $this->flush();
        return $diff;
    }

    public function synchronizeEscrows()
    {
        $escrows = $this->getRepository("StreetVCLoanBundle:Escrow")->findAll();
        $diff = [];
        /** @var Escrow $escrow */
        foreach ($escrows as $escrow) {
            $id = (string) $escrow->getId();
            if ($bancbox_id = $escrow->getBancboxId()) {
                $data = $this->bancbox->getEscrow($bancbox_id);
                $status = $data['status'];
                $remote = $diff[$id]['bancbox'] = $data['current_balance'];
                $notional = $data['notional_balance'];
                $escrow->setNotionalBalance($notional);
                $local = $diff[$id]['local'] = $escrow->getCurrentBalance();
                if ($remote != $local) {
                    $escrow->setCurrentBalance($remote);
                    $this->logger->notice("Updating balance of escrow $bancbox_id from $local to $remote");
                }

            }
        }
        $this->flush();
        return $diff;
    }

    protected function flush()
    {
        $this->om->flush();
    }

    protected function getRepository($className)
    {
        return $this->om->getRepository($className);
    }

}
