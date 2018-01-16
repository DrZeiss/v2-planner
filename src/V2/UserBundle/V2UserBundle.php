<?php

namespace V2\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class V2UserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
