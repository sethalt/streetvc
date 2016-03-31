<?php

namespace Sd\BaseBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    protected function redirectToReferer(Request $request)
    {
        return $this->redirect($request->headers->get('referer'));
    }

    protected function redirectTo($route, $params = array())
    {
        return $this->redirect($this->generateUrl($route, $params));
    }

    protected function getODM()
    {
        return $this->container->get('doctrine.odm.mongodb.document_manager');
    }

    protected function getORM()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    protected function createQueryBuilder($name)
    {
        return $this->getODM()->createQueryBuilder($name);
    }

    protected function getRepository($name)
    {
        return $this->getODM()->getRepository($name);
    }

    protected function isGranted($role)
    {
        return $this->container->get('security.context')->isGranted($role);
    }

    /**
     * @param null|UserInterface $checkUser
     * @return \FOS\UserBundle\Model\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function getUserOrDeny($checkUser = null)
    {
        /* @var UserInterface $user */
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) { //  || !$user instanceof UserInterface) {
            throw new AccessDeniedException('Sorry, you do not have access to this section.');
        }
        if (is_object($checkUser)) {
            if (!$user->getId() == $checkUser->getId()) {
                throw new AccessDeniedException('Sorry, you do not have access to this section.');
            }
        }
        return $user;
    }

    protected function setFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }

    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * Create a form without a name
     * github:voryx/restgeneratorbundle
     * @param null  $type
     * @param null  $data
     * @param array $options
     *
     * @return FormInterface
     */
    public function createForm($type = null, $data = null, array $options = array())
    {
        $factory = $this->get('form.factory');
        $form = $factory->createNamed( null,  $type, $data, $options );

        return $form;
    }

    /**
     * Get rid on any fields that don't appear in the form
     * github:voryx/restgeneratorbundle
     * @param Request $request
     * @param Form    $form
     */
    protected function removeExtraFields(Request $request, Form $form)
    {
        $data = $request->request->all();
        $children = $form->all();
        $data = array_intersect_key($data, $children);
        $request->request->replace($data);
    }
}
