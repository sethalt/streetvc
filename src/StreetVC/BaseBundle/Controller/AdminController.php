<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/10/2014
 * Time: 5:44 PM
 */

namespace StreetVC\BaseBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends BaseController {

    /**
     * @param Request $request
     * @Template("StreetVCBaseBundle:Admin:admin.html.twig")
     */
    public function adminAction(Request $request)
    {

    }

}
