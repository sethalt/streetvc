<?php
namespace StreetVC\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LenderBundle\Document\LoanCommitment;
use StreetVC\BorrowerBundle\Form\Type\LoanRequestFormType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use StreetVC\UserBundle\Document\User;
use StreetVC\ActivityBundle\Form\Type\AddActionFormType;
use StreetVC\ActivityBundle\Document\ActionComponent;
use StreetVC\ActivityBundle\Document\Action;
use StreetVC\ActivityBundle\Form\Model\Action as ActionModel;
use FOS\RestBundle\Request\ParamFetcher;

/** @FOS\RouteResource("User") */
class UsersController extends Controller
{
    /** @FOS\View() */
    public function getAction(Request $request, User $user)
    {
        return;
    }

    /**
     * @FOS\View(templateVar="users")
     * #FOS\QueryParam(name="field", nullable=true, description="search by field")
     * #FOS\QueryParam(name="value", nullable=true, description="value to search in field")
     * #FOS\QueryParam(name="limit", nullable=true, default=5, requirements="\d+", description="number of results to return")
     * #FOS\QueryParam(name="skip", nullable=true, default=0, requirements="\d+", description="number of results to skip")
     * #FOS\QueryParam(name="sort", nullable=true, default="email", requirements="\s+", description="field to sort by")
     */
    public function cgetAction(Request $request) //, ParamFetcher $params)
    {
        return $this->getRepository()->findAll();
        /*
        $sort = null;
        $limit = $params->get('limit');
        $field = $params->get('field');
        $value = $params->get('value');
        $criteria = array($field => $value);

        return $this->getRepository()->findBy($criteria, $sort, $limit, $skip);
        */
    }



    /**
     * @FOS\View()
     * @param Request $request
     * @param User $user
     * @return multitype:\Symfony\Component\Form\FormView
     */
    public function newActionsAction(Request $request, User $user)
    {
        return $this->createForm(new AddActionFormType(), new ActionModel());
    }

    /**
     * @FOS\View()
     * @param Request $request
     * @param User $user
     * @return \FOS\RestBundle\View\View
     */
    public function postActionsAction(Request $request, User $user)
    {
        $form = $this->createForm(new AddActionFormType(), new ActionModel());
        $this->get('streetvc_activity.timeline.add_action.handler')->handle($form, $request);
        return View::createRouteRedirect('get_user_timeline', array('user'=>$user->getId()));
    }

    /**
     * @FOS\View
     * @param Request $request
     * @param User $user
     */
    public function getTransactionsAction(Request $request, User $user)
    {
        $transactions = $this->get('odm')->getRepository('StreetVCTransactionBundle:Transaction')->createQueryBuilder()
        ->field('user')->references($user)
        ->getQuery()->execute();

        return array('transactions' => $transactions->toArray());
    }

    /**
     * @FOS\View(templateVar="timeline")
     * @param Request $request
     * @param User $user
     * @return \StreetVC\ActivityBundle\Document\Timeline
     */
    public function getTimelineAction(Request $request, User $user)
    {
        $subject = 'streetvc';
        $actionManager   = $this->get('spy_timeline.action_manager');
        $timelineManager = $this->get('spy_timeline.timeline_manager');
        $subject         = $actionManager->findOrCreateComponent($user);
        $timeline        = $timelineManager->getTimeline($subject);

        return $timeline;
    }
    
    /** @FOS\View() */
    public function getRoleAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) { //  || !$user instanceof UserInterface) {
            throw new AccessDeniedException('Sorry, you do not have access to this section.');
        }
        $issuer = $this->container->get('security.context')->isGranted('ROLE_ISSUER');
        $investor = $this->container->get('security.context')->isGranted('ROLE_INVESTOR');
        
        switch(true){
            case $issuer && $investor:
                return;
                break;
            case $issuer:
                return View::createRouteRedirect('index_borrower');
                break;
            case $investor:
                return View::createRouteRedirect('invest_index');
                break;
            default: 
                return;
        }

    }
    
    /** @FOS\View() */
    public function cashflowpositiveAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) { //  || !$user instanceof UserInterface) {
            throw new AccessDeniedException('Sorry, you do not have access to this section.');
        }
        $user->setCashflowPositive(true);
        $this->get('odm')->persist($user);
        $this->get('odm')->flush();
        return View::createRouteRedirect('index_borrower');
    }
    

    protected function getRepository()
    {
        return $this->get('odm')->getRepository('StreetVCUserBundle:User');
    }

}