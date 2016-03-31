<?php

namespace StreetVC\LenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LenderBundle\Document\LoanCommitment;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use StreetVC\UserBundle\Document\User;

/** @FOS\RouteResource("LoanCommitment") */
class LoanCommitmentsController extends Controller
{
    protected function getRepository()
    {
        return $this->get('odm')->getRepository('StreetVCBorrowerBundle:LoanCommitment');
    }

    protected function getManager()
    {
        return $this->container->get('street_vc_lender.manager.loan_commitment');
    }

    /** @FOS\View(templateVar="loanCommitments") */
    public function cgetAction()
    {
        $loanCommitments = $this->getManager()->findAll();
        return $loanCommitments;
    }

    /** @FOS\View(templateVar="loanCommitment") */
    public function getAction(Request $request, LoanCommitment $loanCommitment)
    {
        return $loanCommitment;
    }

    /** @FOS\View(template="StreetVCLenderBundle:LoanCommitments:new.html.twig") */
    public function postAction(Request $request)
    {
        return $this->processForm(new LoanCommitment(), 'POST');
    }

    /** @FOS\View() */
    public function newAction(Request $request)
    {
        $form = $this->getForm(new LoanCommitment(), "POST");
        return $form;
    }

    /** @FOS\View(templateVar="loanCommitment") */
    public function postLoanCommitment(Request $request, LoanRequest $loanRequest)
    {
    }

    protected function processForm(LoanCommitment $loanCommitment, $method = 'POST')
    {
        $request = $this->getRequest();
        $form = $this->getForm($loanCommitment, $method);
        $statusCode = $loanRequest->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $ip = $request->getClientIp();
            $loanRequest = $form->getData();
            $this->getManager()->update($loanRequest, true);
            if (!$request->isXmlHttpRequest()) {
                return View::createRouteRedirect('get_loanrequest', array('loanRequest'=>$loanRequest->getId()));
            }
            return new Response(null, $statusCode);
        }
        $response = View::create($form);
        return $response;
    }

    protected function getForm(LoanRequest $loanRequest, $method)
    {
        $form = $this->createForm(new LoanRequestFormType(), new LoanRequest(), array('method'=>$method));
        return $form;
    }
}
