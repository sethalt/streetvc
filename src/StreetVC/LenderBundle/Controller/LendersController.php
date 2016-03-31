<?php

namespace StreetVC\LenderBundle\Controller;

use FOS\RestBundle\View\View;
use GuzzleHttp\Command\Exception\CommandException;
use StreetVC\BancboxInvest\BancboxInvestBundle\Document\FundAccountClickthrough;
use StreetVC\LenderBundle\Document\Lender;
use StreetVC\LenderBundle\Document\LoanCommitment;
use StreetVC\LenderBundle\Event\LenderEvent;
use StreetVC\LenderBundle\Event\LenderEvents;
use StreetVC\LenderBundle\Form\Type\LenderFormType;
use FOS\RestBundle\Controller\Annotations as FOS;
use StreetVC\UserBundle\Document\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sd\BaseBundle\Controller\BaseController;
use Symfony\Component\Form\FormError;
use StreetVC\UserBundle\Form\Type\BankAccountFormType;
use JMS\SecurityExtraBundle\Annotation as Secure;

/**
 * @FOS\RouteResource("Lender")
 * @author dao
 */
class LendersController extends BaseController
{

    /**
     * @FOS\View(templateVar="lender")
     */
    public function cgetAction()
    {
    }

    /**
     * @FOS\View(templateVar="lender")
     * @param Lender $lender
     * @return Lender
     */
    public function getAction(Lender $lender)
    {
        return $lender;
    }

    /**
     * @FOS\View(templateVar="form")
     * @Secure\Secure(roles="ROLE_USER")
     * @return \Symfony\Component\Form\FormView
     */
    public function newAction()
    {
        $user = $this->getUserOrDeny();
        $form = $this->getLenderForm($user);
        $lender = $user->getLender();
        return $form->createView();
    }


    /**
     * @FOS\View(template="StreetVCLenderBundle:Lenders:new.html.twig")
     * @param Request $request
     * @throws \Exception
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->getLenderForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $lender = $user->getLender();
            try {
                $this->get('investor_manager')->createLender($user);
                return $this->redirectTo('invest_index');
            } catch (CommandException $e) {
                $message = $e->getMessage();
                $this->setFlash('error', $message);
                $form->addError(new FormError($message));
            } catch (\InvalidArgumentException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (\Exception $e) {
                throw $e;
            }
        }
        $response = [ 'form' => $form->createView(), 'errors' => $form->getErrors(true,true) ];
        return $response;
    }

    /**
     * @param Request $request
     * @return mixed
     * @FOS\View()
     * @FOS\Get()
     */
    public function synchronizeAction(Request $request)
    {
        $diff = $this->container->get('bancbox.manager')->synchronizeLenders();
        return $diff;
    }

    /**
     * @param User $user
     * @return FormInterface
     */
    public function getLenderForm(User $user)
    {
        $form = $this->createForm(new LenderFormType(), $user);
        return $form;
    }

    /** @FOS\View()
     *
     * @param Request $request
     * @param Lender $lender
     * @return \StreetVC\UserBundle\Form\Type\BankAccountFormType
     */
    public function newAccountAction(Request $request, Lender $lender)
    {
        $form = $this->createForm(new BankAccountFormType());
        return $form;
    }

    /**
     * @FOS\View(template="StreetVCLenderBundle:Lenders:newAccount.html.twig")
     * @param Request $request
     * @param Lender $lender
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|:\Symfony\Component\Form\FormView
     */
    public function postAccountAction(Request $request, Lender $lender)
    {
        $form = $this->createForm(new BankAccountFormType(), null, ['csrf_protection' => false]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $account = $form->getData();
            try {
                $lender->addLinkedAccount($account);
                // @todo strip bancbox link investor
//                $this->get('bancbox_provider')->linkInvestorAccount($lender, $account);
                $this->get('odm')->flush();
                if (!$request->isXmlHttpRequest()) {
                    $this->setFlash('notice', 'created account');
                    return $this->redirectTo('get_lender_accounts', ['lender'=>$lender->getId()]);
                }
                return View::create(null, Response::HTTP_OK);
            } catch (\Exception $e) {
                $this->setFlash('error', $e->getMessage());
                $form->addError(new FormError($e->getMessage()));
            }
        }
        $data = ['form'=>$form->createView(), 'errors' => $form->getErrors(true, true)];
        return View::create($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getAccountAction(Request $request, Lender $lender, $account_id)
    {
        $account = $lender->getAccountById($account_id);

        return ['account' => $account ];
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param Lender $lender
     * @return array
     */
    public function getAccountsAction(Request $request, Lender $lender)
    {
        return ['accounts' => $lender->getLinkedAccounts()];
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param Lender $lender
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postAccountFundsAction(Request $request, Lender $lender)
    {
        $errors = [];

        $account = $request->request->get('account');
        $amount = $request->request->get('amount');
        $memo = $request->request->get('memo', '');
        $user = $this->getUserOrDeny($lender->getUser());

        try {
            $this->getManager()->addFunds($user, $account, $amount, $memo);

            $this->setFlash('notice', "Transferring funds. It will take a few days..");
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->setFlash('error', $message);
        }
        return $this->redirectTo('invest_index');
    }

    public function verifyAccountAction(Request $request, Lender $lender, $account_id)
    {
        $account = $lender->getAccountById($account_id);
        try {
            $challenge_id = $this->get('bancbox_provider')->createChallengeDeposit($lender->getBancboxId(), 'INVESTOR', $account->getBancboxId() );
            $account->setChallengeId($challenge_id);
            $this->get('odm')->flush();
            return $this->redirectTo('get_lender_accounts', ['lender'=>$lender->getId()]);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }
    }

    protected function getManager()
    {
        return $this->get('investor_manager');
    }
}
