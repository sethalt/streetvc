<?php

namespace StreetVC\BorrowerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpFoundation\Request;
use StreetVC\BorrowerBundle\Document\Industry;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

/** @FOS\RouteResource("Industry") */
class IndustriesController extends Controller
{
    protected function getRepository()
    {
        return $this->get('odm')->getRepository('StreetVCBorrowerBundle:Industry');
    }

    /** @FOS\View(templateVar="industries") */
    public function cgetAction()
    {
//        $industries = $this->getRepository()->createQueryBuilder()->hydrate(false)->getQuery()->execute()->toArray();
        $industries = $this->getRepository()->createQueryBuilder()->getQuery()->execute()->toArray(false);
//        return JsonResponse::create(array('industries'=>$industries));
//        return JsonResponse::create($industries);
        return $industries;
    }

    /** @FOS\View(templateVar="industry") */
    public function getAction(Request $request, Industry $industry)
    {
        return $business;
    }

}