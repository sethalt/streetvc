<?php

namespace StreetVC\LoanBundle\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;

use Sd\BaseBundle\Controller\BaseController;
use StreetVC\LoanBundle\Document\Disbursement;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation as Secure;

/**
 * @FOS\RouteResource("Disbursement")
 * @author dao
 *
 */
class DisbursementsController extends BaseController
{
    /**
     * @FOS\View()
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Disbursement $disbursement
     * @return \StreetVC\LoanBundle\Document\Disbursement
     */
    public function getAction(Request $request, Disbursement $disbursement)
    {
        return $disbursement;
    }

    /**
     * @FOS\View()
     */
    public function cgetAction()
    {
        $disbursements = $this->get('odm')->getRepository('StreetVCLoanBundle:Disbursement')->findAll();
        return [ 'disbursements' => $disbursements ];
    }
}
