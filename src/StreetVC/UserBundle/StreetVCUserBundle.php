<?php

namespace StreetVC\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class StreetVCUserBundle extends Bundle
{

    public function getParent()
    {
        return 'FOSUserBundle';
    }

}
