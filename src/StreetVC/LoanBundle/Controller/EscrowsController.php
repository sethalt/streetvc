<?php
namespace StreetVC\LoanBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS;
use Sd\BaseBundle\Controller\BaseController;
use StreetVC\LoanBundle\Document\Escrow;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use JMS\SecurityExtraBundle\Annotation as Secure;

/**
 * @FOS\RouteResource("Escrow")
 * @author dao
 *
 */
class EscrowsController extends BaseController
{
    /**
     * @param Request $request
     * @return mixed
     * @FOS\View()
     */
    public function cgetExpiringAction(Request $request)
    {
        $escrows = $this->getRepository('StreetVCLoanBundle:Escrow')->getExpiringEscrows()->toArray();
        return $escrows;
    }

    /**
     * @FOS\View(templateVar="escrow")
     * @param Escrow $escrow
     * @return \StreetVC\LoanBundle\Document\Escrow
     */
    public function getAction(Request $request, Escrow $escrow)
    {
        return $escrow;
    }

    /**
     * @FOS\View()
     */
    public function cgetAction()
    {
        $escrows = $this->get('odm')->getRepository('StreetVCLoanBundle:Escrow')->findAll();
        return $escrows;
    }

    /**
     * @param Request $request
     * @param Escrow $escrow
     * @return array|\FOS\RestBundle\View\View
     * @FOS\View
     * @FOS\Get
     */
    public function confirmOpenAction(Request $request, Escrow $escrow)
    {
        try {
            $this->getManager()->confirmOpen($escrow);
        } catch (\Exception $e) {
            return ['errors' => $e->getMessage()];
        }
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToReferer($request);
        }
        return View::create();
    }

        /**
     * @param Request $request
     * @param Escrow $escrow
     * @return array|\FOS\RestBundle\View\View
     * @FOS\View
     * @FOS\Get
     */
    public function confirmCloseAction(Request $request, Escrow $escrow)
    {
        $errors = [];
        try {
            $this->getManager()->confirmClose($escrow);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->setFlash('error', $msg);
            $errors[] = $msg;
        }
        if (!$request->isXmlHttpRequest() && $request->getRequestFormat() != 'json') {
            return $this->redirectToReferer($request);
        }
        return ['errors' => $errors];
    }
    /**
     * @param Request $request
     * @param Escrow $escrow
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @FOS\View()
     * @FOS\Get()
     * @FOS\Post()
     */
    public function cancelAction(Request $request, Escrow $escrow)
    {
        try {
            $this->getManager()->cancel($escrow);
            $this->get('odm')->flush();
            $this->setFlash('success', "Canceling escrow.");
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->setFlash('error', "Error canceling escrow: " . $msg);
        }
        return $this->redirectToReferer($request);
    }

    /**
     * @param Request $request
     * @param Escrow $escrow
     * @throws \Exception
     * @return array
     * @FOS\View()
     * @FOS\Get()
     */
    public function payFeeAction(Request $request, Escrow $escrow)
    {
        $errors = [];
        try {
            $this->getManager()->collectOriginationFee($escrow);
        } catch (\Exception $e) {
            $errors = $this->collectErrors($e);
        }
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToReferer($request);
        }
        return $errors;
    }

    protected function collectErrors(\Exception $e) {
        $msg = $e->getMessage();
        $msg = 'Error closing escrow: '.$msg;
        $this->setFlash('error', $msg);
        $errors[] = $msg;
        return $errors;
    }

    /**
     * @param Request $request
     * @param Escrow $escrow
     * @throws \Exception
     * @return array
     * @FOS\View()
     * @FOS\Get()
     */
    public function closeAction(Request $request, Escrow $escrow)
    {
        $errors = [];
        try {
            $this->getManager()->finalize($escrow);
            $this->setFlash('success', "Closing Escrow!");
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $msg = 'Error closing escrow: '.$msg;
            $this->setFlash('error', $msg);
            $errors[] = $msg;
        }
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToReferer($request);
        }
        return ['errors' => $errors ];
    }

    /**
     * @param Request $request
     * @param Escrow $escrow
     * @return array
     * @FOS\View()
     */
    public function getSchedulesAction(Request $request, Escrow $escrow)
    {
        $schedule = $this->getManager()->generateRepaymentSchedule($escrow);
        return ['schedule' => $schedule ];
    }

    /**
     * @return \StreetVC\LoanBundle\Model\EscrowManager
     */
    public function getManager()
    {
        return $this->get('escrow.manager');
    }

    /**
     * @param Request $request
     * @param Escrow $escrow
     * @return array
     */
    public function getCommitmentsAction(Request $request, Escrow $escrow)
    {
        return $escrow->getCommitments()->toArray();
    }

    /**
     * @FOS\View()
     */
    public function postCommitmentsAction(Request $request, Escrow $escrow)
    {
        $errors = [];
        $user = $this->getUserOrDeny();
        if (!$amount = $request->request->get('amount')) {
            $errors[] = 'Missing required field: amount';
            return View::create(['errors'=>$errors],400);
        };
        try {
            $commitment = $this->getManager()->fundEscrow($escrow, $user, $amount);
            $this->setFlash('notice', "Funds escrowed.");
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            return View::create(['errors'=>$errors],400);
        }
        if (!$request->isXmlHttpRequest()) {
            return View::createRouteRedirect('invest_commitments');
        }
        return View::create($commitment, 200);
    }

    /**
     * @FOS\View()
     * admin
     */
    public function synchronizeAction()
    {
        $diff = $this->get('bancbox.manager')->synchronizeEscrows();
        return new JsonResponse($diff);
    }


}
