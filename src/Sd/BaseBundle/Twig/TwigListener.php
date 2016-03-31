<?php
namespace Sd\BaseBundle\Twig;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
class TwigListener
{
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->twig->getExtension('core')->setDateFormat('Y-m-d', '%d days');
        $this->twig->getExtension('core')->setNumberFormat(2, '.', ',');
    }
}