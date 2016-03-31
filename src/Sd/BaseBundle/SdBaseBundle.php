<?php

namespace Sd\BaseBundle;

use Sd\BaseBundle\DependencyInjection\SdBaseExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SdBaseBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerExtension(new SdBaseExtension());
    }
}