<?php

namespace StreetVC\SiteBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation as Secure;
use Sd\BaseBundle\Controller\BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @FOS\RouteResource("")
 */

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="frontend_index")
     * @FOS\View()
     */
    public function indexAction()
    {
    }
    
    /**
     * @Route("/about", name="frontend_about")
     * @FOS\View()
     */
    public function aboutAction()
    {
    }
    
    
    /**
     * @Route("/team", name="frontend_about")
     * @FOS\View()
     */
    public function teamAction()
    {
    }
    
    /**
     * @Route("/how-to", name="frontend_howto")
     * @FOS\View()
     */
    public function howtoAction()
    {
    }
}
