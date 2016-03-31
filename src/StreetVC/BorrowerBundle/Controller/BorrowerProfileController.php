<?php
namespace StreetVC\BorrowerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\BorrowerBundle\Document\Industry;
use StreetVC\BorrowerBundle\Document\BorrowerApplication;
use StreetVC\BorrowerBundle\Form\Type\BusinessFormType;
use StreetVC\BorrowerBundle\Form\Type\BorrowerApplicationFormType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation as Secure;
use Sd\BaseBundle\Controller\BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use StreetVC\BorrowerBundle\Document\MonthlyFinancial;

/**
 * @FOS\RouteResource("borrower")
 */
class BorrowerProfileController extends BaseController
{
    /**
     * @Secure\Secure("ROLE_USER")
     * @Route("/", name="borrower_index")
     * @FOS\View()
     */
    public function indexAction()
    {
        $user = $this->getUser();
        return [ 'user' => $user ];
    }
    
    /**
     * @FOS\View()
     * @Secure\Secure(roles="ROLE_USER")
     */
    public function welcomeAction(Request $request)
    {
        $user = $this->getUserOrDeny();
        return;
    }
    
    /**
     * @FOS\View()
     */
    public function newAction(Request $request)
    {
        $odm = $this->get('odm');
        $user = $this->getUserOrDeny();
        $application = $this->getRepository("StreetVCBorrowerBundle:BorrowerApplication")->findActiveByUserId($user->getId());
        if(!$application){
          $preapproved = $this->getRepository("StreetVCBorrowerBundle:BorrowerApplication")->findPreapprovedByUserId($user->getId());
          if($preapproved){
            return View::createRouteRedirect('preapproved_borrower');
          }else{
            $approved = $this->getRepository("StreetVCBorrowerBundle:BorrowerApplication")->findApprovedByUserId($user->getId());
            if($approved){
              return View::createRouteRedirect('approved_borrower');
            }else{
              $application = new BorrowerApplication();
              $application->setUser($user);
              $application->setState('active');
              $this->get('odm')->persist($application);
              $this->get('odm')->flush();
            }
          }
        }
        $form = $this->createForm(new BorrowerApplicationFormType(), $application, array(
                'method' => 'PUT' 
        ));
        return array('form'=>$form, 'application'=>$application);
    }
    
    /**
     * @FOS\View()
     */
    public function preapprovedAction(Request $request)
    {
        $odm = $this->get('odm');
        $user = $this->getUserOrDeny();
        $application = $this->getRepository("StreetVCBorrowerBundle:BorrowerApplication")->findPreapprovedByUserId($user->getId());
        if(!$application){
            $approved = $this->getRepository("StreetVCBorrowerBundle:BorrowerApplication")->findApprovedByUserId($user->getId());
            if($approved){
                return View::createRouteRedirect('approved_borrower');
            }else{
              return View::createRouteRedirect('new_borrower');
            }
        }
        $form = $this->createForm(new BorrowerApplicationFormType(), $application, array(
                'method' => 'PUT'
        ));
        return array('form'=>$form, 'application'=>$application);
    }

    /**
     * @FOS\View()
     */
    public function approvedAction(Request $request)
    {
        $odm = $this->get('odm');
        $user = $this->getUserOrDeny();
        $application = $this->getRepository("StreetVCBorrowerBundle:BorrowerApplication")->findApprovedByUserId($user->getId());
        if(!$application){
            return View::createRouteRedirect('new_borrower');
        }
        return array('application'=>$application);
    }
    
    /**
     * @FOS\View()
     */
    public function createProfileAction(Request $request)
    {
        $odm = $this->get('odm');
        $user = $this->getUserOrDeny();
        $session = $this->getSession();
        if(!$session->has('business')){
          $business = new Business();
          $session->set('business', $business);
          
        }
        
        return array();
    }
    
     /**
     * @FOS\View()
     */   
    public function profileAction(Request $request)
    {
        $odm = $this->get('odm');
        $session = $this->getSession();
        $business = new Business();
        if ($session->has('business') && $session->get('business') instanceof Business) {
          $business = $session->get('business');
          $business = $odm->merge($business);
          $industry = $odm->getRepository('StreetVCBorrowerBundle:Industry')->findOneBy(array('id'=>$business->getIndustry()->getId()));
          $business->setIndustry($industry);
        }
        $form = $this->getForm($business, "business_profile", "GET");
        if ($request->get('save')) {
          $request = $this->getRequest();
          $form->handleRequest($request);
          if ($form->isValid()) {
            $business = $form->getData();
            $session->set('business', $business);
            return View::createRouteRedirect('financial_borrower');
          }
          
        }
        return array('form'=>$form);
    }
    
     /**
     * @FOS\View()
     */
    public function financialAction(Request $request)
    {
        $session = $this->getSession();
        $business = $session->get('business');
        $mf = new MonthlyFinancial();
        $numPastCashflows = count($business->getPastCashflow()); 
        for($i = $numPastCashflows; $i < 3; $i++){
          switch($i){
              case 0:
                  $mf->setMonth('previousMonth');
                  break;
              case 1: 
                  $mf->setMonth('2months');
                  break;
              case 2: 
                  $mf->setMonth('3months');
                  break;
          }
          $business->addPastCashflow($mf);
        }
        
        $numPastRevenues = count($business->getPastRevenue());
        for($i = $numPastRevenues; $i < 12; $i++){
            $business->addPastRevenue($mf);
        }
        
        $form = $this->getForm($business, "business_financial", "GET");
        if ($request->get('save')) {
          $request = $this->getRequest();
          $form->handleRequest($request);
          if ($form->isValid()) {
            $business = $form->getData();
            $session->set('business', $business);
            return View::createRouteRedirect('review_borrower_profile');
          }
        
        }
        return array('form'=>$form);
    }
    
    /**
     * @FOS\View()
     */
    public function reviewProfileAction(Request $request)
    {
        $odm = $this->get('odm');
        $session = $this->getSession();
        $business = new Business();
        if ($session->has('business') && $session->get('business') instanceof Business) {
          $business = $session->get('business');
          $business = $odm->merge($business);
          $industry = $odm->getRepository('StreetVCBorrowerBundle:Industry')->findOneBy(array('id'=>$business->getIndustry()->getId()));
          $business->setIndustry($industry);
        }
        $form = $this->getForm($business, "business", "POST");
        return array('form'=>$form, 'business'=>$business);
    }
    
    
    /**
     * @FOS\View()
     */
    public function newLoanrequestAction(Request $request, Business $business)
    {
        $loanRequest = new LoanRequest();
        $form = $this->createForm(new LoanRequestFormType(), $loanRequest, array(
                'method' => 'POST'
        ));
        
        return array('form'=>$form, 'business'=>$business);
    }
    
    /** 
     * @FOS\View()
     */
    public function reviewLoanrequestAction(Request $request, LoanRequest $loanRequest)
    {
        $form = $this->createForm( new LoanRequestFormType(), $loanRequest, [
                'action'=> $this->generateUrl('put_loanrequest', ['loanRequest'=>$loanRequest->getId()]),
                'method'=>'PUT'
        ]);
        return array('loanRequest' => $loanRequest, 'form'=>$form);
    }
    
    

    /**
     * @FOS\View()
     */
    public function getLoanrequestAction(Request $request, LoanRequest $loanRequest)
    {
        return array('loanRequest' => $loanRequest);
    }
    
    
    /**
     * @FOS\View()
     */
    public function createEscrowAction(Request $request, LoanRequest $loanRequest)
    {
        return array('loanRequest' => $loanRequest);
    }
    

    /**
     * @FOS\View()
     */
    public function bankingAction(Request $request)
    {
        return array();
    }
    
    /**
     * @FOS\View()
     */
    public function businessAction(Request $request)
    {
        return array();
    }
    
    protected function getForm($business, $type, $method = 'POST')
    {
        $form = $this->createForm($type, $business, array(
                'method' => $method
        ));
        return $form;
    }
}
