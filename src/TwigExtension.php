<?php

namespace Aaronadal\ConfigBundle;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class TwigExtension extends \Twig_Extension
{

    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('param', array($this, 'param')),
        );
    }

    public function param($key)
    {
        return $this->parameterBag->get($key);
    }

}
