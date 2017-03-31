<?php

namespace Aaronadal\Tests\Fixtures;


class AaronadalConfigExtension extends \Aaronadal\ConfigBundle\DependencyInjection\AaronadalConfigExtension
{

    protected function getBundleResourcesDir()
    {
        return __DIR__ . '/../Resources/config';
    }

}