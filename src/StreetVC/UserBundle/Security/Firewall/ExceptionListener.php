<?php
namespace StreetVC\UserBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;


class ExceptionListener // extends BaseExceptionListener
{
    protected function setTargetPath(Request $request)
    {
        // Do not save target path for XHR and non-GET requests
        // You can add any more logic here you want
        if ($request->isXmlHttpRequest() || 'GET' !== $request->getMethod()) {
            return;
        }

        parent::setTargetPath($request);
    }

}
