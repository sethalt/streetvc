<?php

namespace Sd\UserBundle;

use Sd\UserBundle\DependencyInjection\SdUserExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SdUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerExtension(new SdUserExtension());
    }


}
