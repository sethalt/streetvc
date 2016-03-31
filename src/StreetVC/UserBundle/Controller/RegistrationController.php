<?php
namespace StreetVC\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class RegistrationController extends BaseController
{
    
    public function registerAction(Request $request)
    {
        $type = $request->get('type');
        if (!in_array($type, array('borrower', 'lender'))) { 
            throw new AccessDeniedException('Sorry, incorrect url');
        }
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
    
        $user = $userManager->createUser();
        $user->setEnabled(true);
    
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
    
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
    
        $form = $formFactory->createForm();
        $form->setData($user);
    
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
    
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
    
                $userManager->updateUser($user);
    
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('register_role', array('type'=>$type));
                    $response = new RedirectResponse($url);
                }
    
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
    
                return $response;
            }else{
                print_r($form->getErrorsAsString());
            }
        }
          return $this->container->get('templating')->renderResponse('StreetVCUserBundle:Registration:register.html.'.$this->getEngine(), array(
                  'form' => $form->createView(),
                  'type' => $type
          ));
    }
    
    /**
     * Tell the user his account is now confirmed
     */
    public function roleAction(Request $request)
    {
        $type = $request->get('type');
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    
        if($type == "lender"){
          $url = $this->container->get('router')->generate('new_lender');
          return new RedirectResponse($url);
        }elseif($type == "borrower"){
          $url = $this->container->get('router')->generate('welcome_borrower');
          return new RedirectResponse($url);
        }
        
        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:confirmed.html.'.$this->getEngine(), array(
                'user' => $user,
        ));
    }
    
    /** @Template() */
    public function registerChoiceAction()
    {

    }
    
}