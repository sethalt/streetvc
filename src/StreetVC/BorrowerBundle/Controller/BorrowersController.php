<?php
namespace StreetVC\BorrowerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS;
use StreetVC\BaseBundle\Controller\BaseController;
use StreetVC\UserBundle\Form\Type\BankAccountFormType;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\BorrowerBundle\Form\Type\BusinessFormType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @FOS\RouteResource("Issuer")
 */
class BorrowersController extends BaseController
{

    protected $class = 'StreetVCUserBundle:User';

    protected function getRepository($class = null)
    {
        $class = $class ?  : $this->class;
        return $this->get('odm')->getRepository($this->class);
    }

    /**
     * @FOS\View(templateVar="borrowers")
     */
    public function cgetAction()
    {
        $client = $this->get('bancbox_client');
        $borrowers = $client->getIssuerList()->toArray();
        return $borrowers;
        // $businesses = $this->getRepository()->findAll();
        // $borrowers =
        // return $businesses;
    }

    /**
     * @FOS\View(templateVar="issuer")
     * #ParamConverter("business", class="StreetVCBorrowerBundle:Business", options={""})
     */
    public function getAction(Request $request, $id)
    {
        $issuer = $this->get('bancbox_client')->getIssuer([
            'issuer_id' => $id
        ]);
        return $issuer->toArray();
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Borrowers:new.html.twig")
     */
    public function postAction(Request $request)
    {
        $user = $this->getUser();
        // return $this->processForm(new Business(), 'POST');
        $form = $this->generateForm('createIssuer', array(
            'reference_id' => $user->getId()
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
//            return new JsonResponse($data);
            $response = $this->get('bancbox_provider')->createIssuer($data);
            return new JsonResponse($response);
//            return new JsonResponse($form->getData());
        }
        return ['form'=>$form];
    }

    /**
     * @FOS\View()
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->generateForm('createIssuer', [
            'reference_id' => $user->getId()
        ]);
        return $form;
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:edit.html.twig")
     */
    public function putAction(Request $request, Business $business)
    {
        return $this->processForm($business, 'PUT');
    }

    /**
     * @FOS\View()
     */
    public function editAction(Request $request, Business $business)
    {
        return $this->processForm($business, 'PUT');
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
            $this->get('odm')->persist($business);
            $this->get('odm')->flush();
            if (! $request->isXmlHttpRequest()) {
                return View::createRouteRedirect('fos_user_profile_show');
                /*
                return View::createRouteRedirect('get_business', array(
                    'business' => $business->getId()
                ));
                */
            }
            return View::create(null, $statusCode);
        }
        $response = View::create($form);
        return $response;
    }

    protected function getForm(Business $business = null, $method = 'POST')
    {
        $form = $this->createForm(new BusinessFormType(), $business, array(
            'method' => $method
        ));
        return $form;
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:newLoanRequest.html.twig", templateVar="form")
     */
    public function postLoanrequestAction(Request $request, Business $business)
    {
        return $this->processLoanRequestForm($business, new LoanRequest(), 'POST');
    }

    /**
     * @FOS\View(template="StreetVCBorrowerBundle:Businesses:newLoanRequest.html.twig")
     */
    public function newLoanrequestAction(Request $request, Business $business)
    {
        $form = $this->getLoanRequestForm(new LoanRequest(), "POST");
        return $form;
    }

    protected function processLoanRequestForm(Business $business, LoanRequest $loanRequest, $method = 'POST')
    {
        if (! $user = $this->getUser())
            return new AccessDeniedException();
        $form = $this->getLoanRequestForm($loanRequest, $method);
        $request = $this->getRequest();
        $statusCode = $loanRequest->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $loanRequest = $form->getData();
            $loanRequest->setBusiness($business);
            $this->get('odm')->persist($loanRequest);
            $this->get('odm')->flush();
            if (! $request->isXmlHttpRequest()) {
                return View::createRouteRedirect('get_loanrequest', array(
                    'loanRequest' => $loanRequest->getId()
                ));
            }
            return new Response(null, $statusCode);
        }
        return View::create($form);
    }

    protected function getLoanRequestForm(LoanRequest $loanRequest, $method)
    {
        $form = $this->createForm(new LoanRequestFormType(), $loanRequest, array(
            'method' => $method
        ));
        return $form;
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param Business $business
     * @return array
     */
    public function cgetAccountsAction(Request $request, Business $business)
    {
        return ['business' => $business, 'accounts' => $business->getLinkedAccounts()];
    }

    public function postAccountAction(Request $request, Business $business)
    {
        $errors = [];
        $form = $this->createForm(new BankAccountFormType(), null, ['csrf_protection'=>false]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $account = $form->getData();
            try {
//                $response = $this->get('bancbox_provider')->linkIssuerAccount($business, $account);
                $business->addLinkedAccount($account);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                return ['errors' => $errors];
            }
            $this->get('odm')->flush();
            return $account;
        }
        return ['errors' => $errors ];
    }

    protected function generateForm($operationName, array $data = array(), $except = array())
    {
        $description = $this->get('bancbox_client')->getDescription();
        $builder = $this->createFormBuilder($data);
        $operation = $description->getOperation($operationName);
        $params = $operation->getParams();
        $params = array_diff_key($params, array_flip([
            'api_key',
            'secret'
        ]));
        foreach ($params as $key => $param) {
            $type = function ($param) {
                switch ($param->getType()) {
                    case 'integer':
                        return 'integer';
                    case 'number':
                        return 'number';
                    case 'boolean':
                        return 'checkbox';
                    case 'string':
                        return 'text';
                    default:
                        return null;
                }
            };
            $builder->add($param->getName(), $type($param), [
                'required' => $param->getRequired()
            ]);
        }

        return $builder->getForm();
    }
}
