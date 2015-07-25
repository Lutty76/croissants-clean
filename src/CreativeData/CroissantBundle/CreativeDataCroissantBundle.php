<?php

namespace CreativeData\CroissantBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
class CreativeDataCroissantBundle extends Bundle
{
    public function getParent()
    {
        return 'HWIOAuthBundle';
    }
}
