<?php
namespace StreetVC\UserBundle\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{

    protected $httpUtils;
    protected $targetUrl;

    /**
     * @param HttpUtils $httpUtils
     * @param string    $targetUrl
     */
    public function __construct(HttpUtils $httpUtils, $targetUrl = '/')
    {
        $this->httpUtils = $httpUtils;
        $this->targetUrl = $targetUrl;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        if ($request->isXmlHttpRequest()) {
            $result = array('success' => true);
            $response = new JsonResponse($result);
            return $response;
        }
        return $this->httpUtils->createRedirectResponse($request, $this->targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        if ($request->isXmlHttpRequest()) {
            $result = array('success' => false, 'message' => $exception->getMessage());
            $response = new JsonResponse($result);
            return $response;
        }

        $request->getSession()->getFlashBag()->set('error', $exception->getMessage());
        $request->getSession()->set(SecurityContext::AUTHENTICATION_ERROR, $exception);
        return $this->httpUtils->createRedirectResponse($request, $this->targetUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = array('success' => true);
            $response = new JsonResponse($result);
            return $response;
        }
        return $this->httpUtils->createRedirectResponse($request, $this->targetUrl);
    }

}