<?php
namespace StreetVC\UserBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionListener
{

    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if (!$request->isXmlHttpRequest()) {
            return;
        }

        // Assume a server error if no explicit code is given
        $statusCode = $exception->getCode();
        if (!array_key_exists($statusCode, Response::$statusTexts)) {
            $statusCode = 500;
        }

        $content = array('success' => false, 'message' => $exception->getMessage());
        $response = new JsonResponse($content, $statusCode);

        $event->setResponse($response);
    }
}
