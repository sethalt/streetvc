<?php
namespace StreetVC\BorrowerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS;
use StreetVC\BaseBundle\Controller\BaseController;
use StreetVC\UserBundle\Form\Type\BankAccountFormType;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\BorrowerBundle\Document\BorrowerApplication;
use StreetVC\BorrowerBundle\Form\Type\BorrowerApplicationFormType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation as Secure;

/**
 * @FOS\RouteResource("BorrowerApplication")
 */
class BorrowerApplicationController extends BaseController
{

    protected $class = 'StreetVCBorrowerBundle:BorrowerApplication';
    
    protected function getRepository($class = null)
    {
        $class = $class ?  : $this->class;
        return $this->get('odm')->getRepository($this->class);
    }

    /**
     * @FOS\View()
     * @Secure\Secure(roles="ROLE_USER")
     */
    public function newAction(Request $request)
    {
        $user = $this->getUserOrDeny();
        $application = $user->getOrCreateBorrowerApplication();
        $form = $this->getForm($application, "POST");
        return $form;
    }
    
    /**
     * @FOS\View()
     * @Secure\Secure(roles="ROLE_USER")
     */
    public function postAction(Request $request)
    {
        $user = $this->getUser();
        $application = $user->getOrCreateBorrowerApplication();
        return $this->processForm(new BorrowerApplication(), 'POST');
    }

    
    
    public function putAction(Request $request, BorrowerApplication $application)
    {
        return $this->processForm($application, 'PUT');
    }
    
    /**
     * @FOS\View()
     */
    public function editAction(Request $request, BorrowerApplication $application)
    {
        return $this->processForm($application, 'PUT');
    }
    
    protected function getForm(BorrowerApplication $application, $method = 'POST')
    {
        $form = $this->createForm(new BorrowerApplicationFormType(), $application, array(
                'method' => $method
        ));
        return $form;
    }
    
    protected function processForm(BorrowerApplication $application, $method = 'POST')
    {
        if (! $user = $this->getUser())
            return new AccessDeniedException();
    
        $request = $this->getRequest();
        $form = $this->getForm($application, $method);
        $statusCode = $application->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $application = $form->getData();
            $this->get('odm')->persist($application);
            $this->get('odm')->flush();
            /*
            if (! $request->isXmlHttpRequest()) {
                return View::createRouteRedirect('new_borrower_loanrequest', array(
                        'business' => $business->getId()
                ));
            }
            */
            return View::create(null, $statusCode);
    
        }
        $response = View::create($form);
        return $response;
    }
    
    
    /**
     * @FOS\View()
     */
    public function getPreapprovalAction(Request $request, BorrowerApplication $application)
    {
        $message = "Success!";
        $status = "declined";
        $today = new \DateTime();
        $year = $today->format('Y');
        $interval = $year - $application->getYearEstablished();
        
        $revenue = $application->getAnnualRevenue();
        
        $ownership = $application->getGuarantorBusinessOwnership();
        
        $birthdate = $application->getDateOfBirth();
        
        if($birthdate){
        $age = \DateTime::createFromFormat('d/m/Y', $birthdate->format('d/m/Y'))
        ->diff(new \DateTime('now'))
        ->y;
        }
        
        if($interval < 2){
           $message = "Your business needs to be at least 2 years old.";
        }elseif($revenue < 100000){
           $message = "Your business needs to have at least $100,000 in revenue";
        }elseif($ownership <= 20){
           $message = "Your % ownership of your business needs to be greater than 20%";
        }elseif($age && $age <= 22){
           $message = "You must be over 22 years of age.";
        }else{
            $application->setState('preapproved');
            $this->get('odm')->persist($application);
            $this->get('odm')->flush();
            $status = "preapproved";
        }
        
        return array('message'=>$message, 'status'=>$status); 
    }
    
    
    /**
     * @FOS\View()
     */
    public function getApprovalAction(Request $request, BorrowerApplication $application)
    {
      $application->setState('approved');
      $this->get('odm')->persist($application);
      $this->get('odm')->flush();
      $message = "You have been approved!";
      $status = "approved";
      return array('message'=>$message, 'status'=>$status);
    }

}