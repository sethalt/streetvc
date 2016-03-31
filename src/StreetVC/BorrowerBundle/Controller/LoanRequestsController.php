<?php

namespace StreetVC\BorrowerBundle\Controller;

use Finite\Exception\StateException;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\View\View;
use StreetVC\BaseBundle\Document\Contract;
use StreetVC\BaseBundle\Document\Contract\OpenEscrowContract;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use Finite\StateMachine\StateMachineInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;
use Sd\BaseBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** @FOS\RouteResource("LoanRequest") */
class LoanRequestsController extends BaseController
{
    /** @FOS\View() */
    public function cgetAction(Request $request)
    {
        parse_str($_SERVER['QUERY_STRING'], $httpquery);
        
        $min = $request->request->get('minAmount'); //$httpquery['minAmount'];
        $max = $request->request->get('maxAmount'); // $httpquery['maxAmount'];
        $industry = $request->request->get('industry'); //$httpquery['industry'];
        $purpose = $request->request->get('purpose'); //$httpquery['purpose'];
        $zipCode = $request->request->get('zipCode'); //$httpquery['zipCode'];
        $creditRating = $request->request->get('creditRating'); //$httpquery['creditRating'];
        
        $qb = $this->get('odm')->getRepository('StreetVCLoanBundle:LoanRequest')->createQueryBuilder();
          $qb->field('state')->in(['escrow']);
          if($min){ $qb->field('funding_goal')->gte((int) $min); }
          if($max){ $qb->field('funding_goal')->lte((int) $max); }
          if($purpose){ $qb->field('use_of_loan_proceeds')->equals( $purpose); }
        $requests = $qb->getQuery()->execute()->toArray();
        return ['requests' => $requests ];
    }

    /** @FOS\View(templateVar="loanRequest") */
    public function getAction(Request $request, LoanRequest $loanRequest)
    {
        return $loanRequest;
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return multitype:\Symfony\Component\Form\FormView
     */
    public function editAction(Request $request, LoanRequest $loanRequest)
    {
        $form = $this->createForm( new LoanRequestFormType(), $loanRequest, [
            'action'=> $this->generateUrl('put_loanrequest', ['loanRequest'=>$loanRequest->getId()]),
            'method'=>'PUT'
        ]);
        return ['form'=>$form->createView(), 'errors'=>$form->getErrors(true, true)];
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:LoanRequests:edit.html.twig")
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function putAction(Request $request, LoanRequest $loanRequest)
    {
        $override = $request->getHttpMethodParameterOverride();
        $method = $request->getMethod();
        $form = $this->createForm(new LoanRequestFormType(), $loanRequest, [
            'method' => 'PUT',
            'action' => $this->generateUrl('put_loanrequest', ['loanRequest'=>$loanRequest->getId() ])
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $loanRequest = $form->getData();
            $this->get('odm')->flush();
            $this->setFlash('notice', "Updated Loan Request");
            if (!$request->isXmlHttpRequest()) {
              return $this->redirectToLoanRequest($loanRequest);
            }
        }
        $errors = $form->getErrors(true, true);
        $detail = (string) $errors;
        return ['form'=>$form->createView(), 'errors' => $errors ];
    }

    /**
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @FOS\Get
     */
    public function submitAction(Request $request, LoanRequest $loanRequest)
    {
        try {
            $this->getManager()->submit($loanRequest);
            $this->setFlash('success', 'Loan Request submitted');
        } catch (StateException $e) {
            $this->setFlash('error', "Could not submit Loan Request from state ".$loanRequest->getFiniteState());
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->setFlash('error', $msg);
        }
        if (!$request->isXmlHttpRequest()) {
//            return $this->redirectToLoanRequest($loanRequest);
            return $this->redirectToReferer($request);
        }
    }

    /**
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @FOS\View()
     * @FOS\Get()
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction(Request $request, LoanRequest $loanRequest)
    {
        $user = $this->getUserOrDeny($loanRequest->getUser());
        $business = $user->getBusiness();
        try {
            $this->getManager()->cancel($loanRequest);
            $this->get('odm')->flush();
        } catch (StateException $e) {
            $this->setFlash('error', "Could not cancel from state " . $loanRequest->getFiniteState());
            return $this->redirectToLoanRequest($loanRequest);
        }
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectTo('new_business_loanrequest', ['business' => $business]);
        }
        return View::create();
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return Response
     */
    public function getStateAction(Request $request, LoanRequest $loanRequest)
    {
        $machine = $this->getStateMachine($loanRequest);
        $state = $machine->getCurrentState();

        return ['state'=>$state];
    }

    /**
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return View
     * @FOS\Get
     */
    public function unsetAction(Request $request, LoanRequest $loanRequest)
    {
        $business = $loanRequest->getBusiness();
        $business->unsetActiveLoanRequest();
        $this->getODM()->flush();
        return View::create();
    }

    /**
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function acceptTermsAction(Request $request, LoanRequest $loanRequest)
    {
        try {
            $this->getManager()->acceptTerms($loanRequest);
            $this->setFlash('success', "Terms Accepted");
        } catch (\Exception $e) {
            $this->setFlash('error', $e->getMessage());
        }
        if (!$request->isXmlHttpRequest()) {
         // return $this->redirectToLoanRequest($loanRequest);
            return $this->redirectTo('create_borrower_escrow', ['loanRequest'=>$loanRequest->getId()]);
        }
    }

    /**
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @FOS\View()
     * @FOS\Get()
     */
    public function postEscrowAction(Request $request, LoanRequest $loanRequest)
    {
        $m = $this->get('escrow.manager');
        try {
            $escrow = $m->createEscrow($loanRequest);
            $this->setFlash("notice", "Escrow created");
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error creating escrow: '. $e->getMessage());
        }
        if (!$request->isXmlHttpRequest()) {
//            return $this->redirectToLoanRequest($loanRequest);
            return $this->redirectTo('index_borrower');
        }
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return OpenEscrowContract
     */
    public function getContractAction(Request $request, LoanRequest $loanRequest)
    {
        if (!$contract = $loanRequest->getContract()) {
            $contract = new Contract();
            $filename = 'sampleagreement.pdf';
            $root = $this->container->getParameter('kernel.root_dir');
            $filepath = $root . "/../web/" . $filename;
            $contract->setContentType('application/pdf');
            $contract->setFile($filepath);
            $contract->setFilename($filename);
            $loanRequest->setContract($contract);
            $this->get('odm')->persist($contract);
            $this->get('odm')->flush();
        }
        if ($request->query->get('download', false)) {
            $stream = $contract->getStream();
            $getBytes = function() use ($stream) { fpassthru($stream); };
            $headers = ['Content-Type'=>$contract->getContentType()];
            return StreamedResponse::create($getBytes, 200, $headers);
        }
        return $contract;
    }

   /**
     * @FOS\View()
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return multitype:\StreetVC\TransactionBundle\Traits\unknown
     */
    public function patchStateAction(Request $request, LoanRequest $loanRequest)
    {
        $state = $request->request->get('state');
        /** @var StateMachineInterface */
        $sm = $this->getStateMachine($loanRequest);
        $sm->apply($state);
        $this->get('odm')->flush();
        $state = $sm->getCurrentState();

        return ['state'=>$state];
    }

    /**
     * @FOS\View()
     * @FOS\Get()
     * @param Request $request
     * @param LoanRequest $loanRequest
     * @return multitype:unknown
     */
    public function patchTransitionAction(Request $request, LoanRequest $loanRequest)
    {
        return $this->applyTransition($request, $loanRequest);
    }

    private function applyTransition(Request $request, LoanRequest $loanRequest)
    {
        $transition = $request->query->get('transition');
        $sm = $this->getStateMachine($loanRequest);

        try {
            $sm->apply($transition);
            $this->get('odm')->flush();
            if (!$request->isXmlHttpRequest()) {
              return $this->redirectToLoanRequest($loanRequest);
            }
        } catch (\Exception $e) {
            $m = $e->getMessage();
            $this->setFlash('notice', $m);
            throw $e;
        }
        $state = $sm->getCurrentState();

        return ['state'=>$state];

    }

    public function redirectToLoanRequest(LoanRequest $loanRequest)
    {
        return $this->redirectTo('get_loanrequest', ['loanRequest'=>$loanRequest->getId()]);
    }

    /**
     * @param $object
     * @return StateMachineInterface
     */
    protected function getStateMachine($object)
    {
        $factory = $this->get('finite.factory');
        $machine = $factory->get($object);
        return $machine;
    }

    /**
     * @return \StreetVC\LoanBundle\Model\LoanRequestManager
     */
    public function getManager()
    {
        return $this->get('loanrequest.manager');
    }

}
