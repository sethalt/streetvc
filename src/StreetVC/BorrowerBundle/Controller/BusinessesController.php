<?php
namespace StreetVC\BorrowerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS;
use StreetVC\BorrowerBundle\Event\BusinessEvent;
use StreetVC\BorrowerBundle\Event\BusinessEvents;
use StreetVC\UserBundle\Form\Type\BankAccountFormType;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\BorrowerBundle\Document\Business;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use Symfony\Component\Form\FormError;
use Sd\BaseBundle\Controller\BaseController;
use JMS\SecurityExtraBundle\Annotation as Secure;

/**
 * @FOS\RouteResource("Business")
 */
class BusinessesController extends BaseController
{

    protected $class = 'StreetVCBorrowerBundle:Business';

    protected function getRepository($class = null)
    {
        $class = $class ?  : $this->class;
        return $this->get('odm')->getRepository($this->class);
    }

    /**
     * @FOS\View(templateVar="businesses")
     */
    public function cgetAction()
    {
        $businesses = $this->getRepository()->findAll();
        return $businesses;
    }

    /**
     * @FOS\View(templateVar="business")
     * @param Request $request
     * @param Business $business
     * @return \FOS\RestBundle\View\View|\StreetVC\BorrowerBundle\Document\Business
     */
    public function getAction(Request $request, Business $business)
    {
        return $business;
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:new.html.twig")
     * @Secure\Secure(roles="ROLE_USER")
     */
    public function postAction(Request $request)
    {
        $user = $this->getUser();
        $business = $user->getOrCreateBusiness();
        return $this->processForm(new Business(), 'POST');
    }

    /**
     * @FOS\View()
     * @Secure\Secure(roles="ROLE_USER")
     */
    public function newAction(Request $request)
    {
        $user = $this->getUserOrDeny();
        $business = $user->getOrCreateBusiness();
        $form = $this->getForm($business, "POST");
        return $form;
    }


    public function putAction(Request $request, Business $business)
    {
        return $this->processForm($business, 'PUT');
    }

    /**
     * @FOS\View()
     * @Secure\Secure(roles="ROLE_ISSUER")
     */
    public function editAction(Request $request, Business $business)
    {
        return $this->processForm($business, 'PUT');
    }

    /**
     * @FOS\View()
     * @Secure\Secure(roles="ROLE_USER")
     */
    public function submitAction(Request $request)
    {
        $user = $this->getUserOrDeny();
        $odm = $this->get('odm');
        $session = $this->getSession();

        $business = $session->get('business');
        $business = $odm->merge($business);
        $industry = $odm->getRepository('StreetVCBorrowerBundle:Industry')->findOneBy(array('id'=>$business->getIndustry()->getId()));
        $business->setIndustry($industry);

        $form = $this->getForm($business, 'POST');
        $statusCode = $business->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
            $business = $form->getData();
            $business->setUser($user);
            $user->setBusiness($business);
            $this->get('odm')->persist($business);
            $this->get('odm')->flush();

        // @todo remove bancbox
            if (! $business->getBancboxId()) {
                /**
                try {
                    $this->get('bancbox_provider')->createIssuer($user, $business);
                } catch (\Exception $e) {
                    $form->addError(new FormError($e->getMessage()));
                    return [ 'form' => $form->createView() ];
                }
                 */
                $this->get('dispatcher')->dispatch(BusinessEvents::CREATED, new BusinessEvent($business));
                $this->get('odm')->flush();
            }
            if (! $request->isXmlHttpRequest()) {
                return View::createRouteRedirect('new_borrower_loanrequest', array(
                        'business' => $business->getId()
                ));
            }
            return View::create(null, $statusCode);
    }


    protected function processForm(Business $business, $method = 'POST')
    {
        if (! $user = $this->getUser())
            return new AccessDeniedException();

        $request = $this->getRequest();
        $form = $this->getForm($business, $method);
        $statusCode = $business->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $business = $form->getData();
            $business->setUser($user);
            $user->setBusiness($business);
            $this->get('odm')->persist($business);
            $this->get('odm')->flush();

            if (! $business->getBancboxId()) {
                try {
                    $this->get('bancbox_provider')->createIssuer($user, $business);
                } catch (\Exception $e) {
                    $form->addError(new FormError($e->getMessage()));
                    return [ 'form' => $form->createView() ];
                }
                $this->get('dispatcher')->dispatch(BusinessEvents::CREATED, new BusinessEvent($business));
                $this->get('odm')->flush();
            }
            if (! $request->isXmlHttpRequest()) {
                return View::createRouteRedirect('new_borrower_loanrequest', array(
                        'business' => $business->getId()
                ));
            }
            return View::create(null, $statusCode);

        }
        $response = View::create($form);
        return $response;
    }

    protected function getForm(Business $business, $method = 'POST')
    {
        $form = $this->createForm('business', $business, array(
            'method' => $method
        ));
        return $form;
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:newLoanRequest.html.twig", templateVar="form")
     */
    public function postLoanrequestAction(Request $request, Business $business)
    {
        return $this->processLoanRequestForm($request, $business->createLoanRequest());
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:newLoanRequest.html.twig")
     */
    public function newLoanrequestAction(Request $request, Business $business)
    {
        return $this->processLoanRequestForm($request, $business->createLoanRequest());
    }

    protected function processLoanRequestForm(Request $request, LoanRequest $loanRequest)
    {
        if (! $user = $this->getUser()) {
            return new AccessDeniedException();
        }

        $form = $this->createForm(new LoanRequestFormType(), $loanRequest, array(
            'method' => 'POST'
        ));
        $statusCode = $loanRequest->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $loanRequest = $form->getData();
            $this->get('odm')->persist($loanRequest);
            $this->get('odm')->flush();
            if (! $request->isXmlHttpRequest()) {
                return View::createRouteRedirect('review_borrower_loanrequest', array(
                    'loanRequest' => $loanRequest->getId()
                ));
            }
            return new Response(null, $statusCode);
        }
        $errors = $form->getErrors(true, true);
        return array( 'form' => $form, 'errors' => $errors );
    }

    /**
     * @param Request $request
     * @param Business $business
     * @return mixed
     * @FOS\View()
     * @FOS\Post()
     */
    public function withdrawAction(Request $request, Business $business)
    {
        $user = $this->getUser();
        if (!$user == $business->getUser()) {
            throw new AccessDeniedHttpException();
        }
        $amount = $request->request->get('amount');
        $account_id = $request->request->get('account');
        $memo = $request->request->get('memo', '');

        try {
            $account = $business->getAccountById($account_id);
            $result = $this->getManager()->withdrawFunds($business, $account, $amount, $memo);
            $this->get('odm')->flush();
            $this->setFlash('success', "Funds successfully withdrawn");
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectTo('index_borrower');
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            return View::create(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        return View::create(null, Response::HTTP_OK);
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param Business $business
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postAccountFundsAction(Request $request, Business $business)
    {
        $errors = [];

        $account = $request->request->get('account');
        $amount = $request->request->get('amount');
        $memo = $request->request->get('memo', '');
        $user = $this->getUserOrDeny($business->getUser());

        try {
            $this->getManager()->addFunds($user, $account, $amount, $memo);

            $this->setFlash('notice', "Transferring $amount. It will take a few days..");
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->setFlash('error', $message);
        }
        return $this->redirectTo('index_borrower');
    }
    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:newAccount.html.twig")
     * @param Request $request
     * @param Business $business
     * @return \Symfony\Component\HttpFoundation\RedirectResponse:\Symfony\Component\Form\FormView
     */
    public function postAccountAction(Request $request, Business $business)
    {
        $user = $this->getUser();
        if (!$business->getUser() == $user) {
            return new AccessDeniedException();
        }
        $form = $this->createForm(new BankAccountFormType(), null, ['csrf_protection' => false]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $account = $form->getData();
            try {
                $this->getManager()->linkAccount($business, $account);
                if (!$request->isXmlHttpRequest()) {
                    $this->setFlash('notice', 'created account');
                    return $this->redirectTo('get_business', ['business'=>$business->getId()]);
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

    protected function getManager()
    {
        return $this->get('issuer_manager');
    }
}
