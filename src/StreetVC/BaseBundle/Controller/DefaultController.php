<?php

namespace StreetVC\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StreetVCBaseBundle:Default:index.html.twig', array('name' => $name));
    }
}
