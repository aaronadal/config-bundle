<?php

namespace Aaronadal\ConfigBundle;


use Aaronadal\ConfigBundle\DependencyInjection\LoadConfigCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class AaronadalConfigBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoadConfigCompilerPass());
    }

}
