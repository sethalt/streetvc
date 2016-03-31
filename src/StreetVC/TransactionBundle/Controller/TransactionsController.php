<?php

namespace StreetVC\TransactionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use StreetVC\UserBundle\Document\User;
use StreetVC\TransactionBundle\Document\Transaction;

/**
 * @FOS\RouteResource("Transaction")
 */
class TransactionsController extends Controller
{
    protected function getRepository()
    {
        return $this->get('odm')->getRepository('StreetVCTransactionBundle:Transaction');
    }
    protected function getTransactions()
    {
        return $this->getRepository()->findAll();
    }

    protected function getTransaction($criteria)
    {
        return $this->getRepository()->find($criteria);
    }

    protected function getForm($transaction = null)
    {
        $transaction = $transaction ?: new Transaction();
        $builder = $this->createFormBuilder($transaction);
        $builder->add('amount');
        $builder->add('description');
        return $builder->getForm();
    }

    /**
     * @FOS\View
     * @param Request $request
     */
    public function cgetAction(Request $request)
    {
        return $this->getTransactions();
    }

    /**
     * @FOS\View
     * @param Request $request
     * @param Transaction $transaction
     */
    public function getAction(Request $request, Transaction $transaction)
    {

    }

    /**
     * @FOS\View
     * @param Request $request
     */
    public function newAction(Request $request)
    {
        $form = $this->getForm(new Transaction());
        return array('form'=>$form);
    }

    /**
     * @FOS\View
     * @param Request $request
     */
    public function postAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->getForm(new Transaction());
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $transaction = $form->getData();
            $transaction->setUser($user);
            $this->get('odm')->persist($transaction);
            $this->get('odm')->flush();
            return View::createRouteRedirect('get_user_transactions', array('user'=>$user->getId()));
        }
    }

}
