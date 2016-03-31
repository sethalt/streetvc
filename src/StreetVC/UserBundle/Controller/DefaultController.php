<?php

namespace StreetVC\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('StreetVCUserBundle::login_register.html.twig');
    }

}
