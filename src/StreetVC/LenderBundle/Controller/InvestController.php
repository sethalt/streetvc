<?php

namespace StreetVC\LenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sd\BaseBundle\Controller\BaseController;
use JMS\SecurityExtraBundle\Annotation as Secure;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use StreetVC\BancboxInvest\BancboxInvestBundle\Model\FundAccountClickthrough;
use StreetVC\LoanBundle\Document\LoanRequest;

class InvestController extends BaseController
{

    /**
     * @Secure\Secure("ROLE_USER")
     * @Route("/", name="invest_index")
     * @View()
     */
    public function indexAction()
    {
        $user = $this->getUser();
        return [ 'user' => $user ];
    }

    /**
     * @View()
     * @Route("/ledger", name="invest_ledger")
     * @throws Exception
     */
    public function ledgerAction()
    {
        $user = $this->getUser();
        $investorId = $user->getInvestorId();
        try {
            $response = $this->get('bancbox_provider')->getInvestorLedger($investorId);
            $ledger = $response['ledger'];
        } catch (\Exception $e) {
            throw $e;
        }
        return [ 'user' => $user, 'ledger' => $ledger ];
    }

    /**
     * @View()
     * @Route("/activity", name="invest_activity")
     * @throws Exception
     */
    public function activityAction()
    {
        $user = $this->getUser();
        $investorId = $user->getInvestorId();
        try {
            $response = $this->get('bancbox_provider')->getInvestorActivity($investorId);
            $activity = $response['activity'];
        } catch (\Exception $e) {
            throw $e;
        }
        $lender = $user->getLender();
        $account = $lender->getInternalAccount();
        $commitments = $this->get('odm')->createQueryBuilder('StreetVCLenderBundle:LoanCommitment')
            ->field('user')->equals($user->getId())
            ->sort('created', 'DESC')
            ->limit(5)
            ->getQuery()->execute();
        return [ 'user' => $user, 'activity' => $activity, 'account' => $account, 'lender' => $lender, 'commitments'=>$commitments ];
    }

    /**
     * @View()
     * @Route("/commitments", name="invest_commitments")
     */
    public function commitmentsAction()
    {
        $user = $this->getUser();
        $lender = $user->getLender();
//        $data = $this->get('bancbox_provider')->getInvestor($lender->getBancboxId())->toArray();
//        $investments = $data['investments'];
        $commitments = $this->get('odm')->getRepository('StreetVCLenderBundle:LoanCommitment')->findBy(array('user'=>$user->getId()));
        return ['commitments' => $commitments, 'lender' => $lender];
    }

    /**
     * @View()
     * @Route("/accounts", name="invest_accounts")
     * @throws Exception
     */
    public function accountsAction()
    {
        $user = $this->getUser();
        $accounts = $user->getLender()->getLinkedAccounts();
        return [ 'accounts' => $accounts, 'user' => $user ];
    }

    /**
     * @View()
     * @Route("/account", name="invest_account")
     * @Secure\Secure(roles="ROLE_USER")
     * @return multitype:unknown
     */
    public function accountAction()
    {
        $user = $this->getUser();
        $lender = $user->getLender();
        $account = $lender->getInternalAccount();
//        $form = $this->createForm('fund_account', new FundAccountClickthrough(), array('accounts'=>$lender->getLinkedAccounts()));
        return [ 'account' => $account, 'lender' => $lender ];
    }

    /**
     * * @Route("/loanrequest/{loanRequest}", name="invest_loanrequest")
     * @View()
     */
    public function viewLoanRequestAction($loanRequest)
    {
        $lr = $this->get('odm')->getRepository('StreetVCLoanBundle:LoanRequest')->findOneBy(array('id'=>$loanRequest));
        return ['loanRequest' => $lr];
    }

}
