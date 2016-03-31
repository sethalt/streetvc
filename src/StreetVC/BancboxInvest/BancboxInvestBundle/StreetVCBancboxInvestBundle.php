<?php

namespace StreetVC\BancboxInvest\BancboxInvestBundle;

use StreetVC\BancboxInvest\BancboxInvestBundle\DependencyInjection\StreetVCBancboxInvestExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StreetVCBancboxInvestBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new StreetVCBancboxInvestExtension();
    }
}
