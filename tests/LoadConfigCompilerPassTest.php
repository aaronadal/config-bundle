<?php

namespace Aaronadal\Tests;


use Aaronadal\ConfigBundle\DependencyInjection\LoadConfigCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class LoadConfigCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var LoadConfigCompilerPass
     */
    private $pass;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->pass      = new LoadConfigCompilerPass();

        $this->setEnvironment();
        $this->container->setParameter('aaronadal.config.location.defaults', __DIR__ . '/assets/defaults/*');
    }

    private function setEnvironment($environment = 'none')
    {
        $this->container->setParameter(
            'aaronadal.config.location.environment',
            __DIR__ . '/assets/' . $environment . '/*'
        );
    }

    public function testDefaultParametersAreLoaded()
    {
        $this->pass->process($this->container);

        $this->assertTrue($this->container->hasParameter('only-default'));
        $this->assertSame('only-default', $this->container->getParameter('only-default'));

        $this->assertTrue($this->container->hasParameter('overriden'));
        $this->assertSame('not-overriden', $this->container->getParameter('overriden'));

        $this->assertFalse($this->container->hasParameter('only-dev'));
        $this->assertFalse($this->container->hasParameter('only-prod'));
    }

    public function testDevDomainParametersAreLoaded()
    {
        $this->setEnvironment('dev');
        $this->pass->process($this->container);

        $this->assertTrue($this->container->hasParameter('only-dev'));
        $this->assertSame('only-dev', $this->container->getParameter('only-dev'));

        $this->assertFalse($this->container->hasParameter('only-prod'));
    }

    public function testProdDomainParametersAreLoaded()
    {
        $this->setEnvironment('prod');
        $this->pass->process($this->container);

        $this->assertFalse($this->container->hasParameter('only-dev'));

        $this->assertTrue($this->container->hasParameter('only-prod'));
        $this->assertSame('only-prod', $this->container->getParameter('only-prod'));
    }

    public function testDefaultParametersAreOverriden()
    {
        $this->setEnvironment('dev');
        $this->pass->process($this->container);

        $this->assertTrue($this->container->hasParameter('overriden'));
        $this->assertSame('dev-overriden', $this->container->getParameter('overriden'));
    }

    public function testDefaultServicesAreLoaded()
    {
        $this->pass->process($this->container);

        $this->assertTrue($this->container->has('service.default'));

        $this->assertFalse($this->container->has('service.dev'));
        $this->assertFalse($this->container->has('service.prod'));
    }

    public function testDevDomainServicesAreLoaded()
    {
        $this->setEnvironment('dev');
        $this->pass->process($this->container);

        $this->assertTrue($this->container->has('service.dev'));
        $this->assertFalse($this->container->has('service.prod'));
    }

    public function testProdDomainServicesAreLoaded()
    {
        $this->setEnvironment('prod');
        $this->pass->process($this->container);

        $this->assertFalse($this->container->has('service.dev'));
        $this->assertTrue($this->container->has('service.prod'));
    }

    public function testDefaultServicesAreOverriden()
    {
        $this->pass->process($this->container);

        $this->assertTrue($this->container->has('service.overriden'));
        $this->assertSame(
            'ServiceOverridenDummyClass',
            $this->container->getDefinition('service.overriden')->getClass()
        );

        $this->setUp();
        $this->setEnvironment('dev');
        $this->pass->process($this->container);

        $this->assertTrue($this->container->has('service.overriden'));
        $this->assertSame(
            'ServiceOverridenDevDummyClass',
            $this->container->getDefinition('service.overriden')->getClass()
        );
    }

}
