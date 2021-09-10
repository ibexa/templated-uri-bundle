<?php

namespace Hautelook\TemplatedUriBundle\Tests\DependencyInjection;

use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContainerTest extends TestCase
{
    private function getContainer(array $configs = array())
    {
        $container = new ContainerBuilder();

        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.environment', 'test');
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', tempnam(sys_get_temp_dir(), "HautelookTemplatedUriBundle"));
        $container->setParameter('kernel.bundles', array('HautelookTemplatedUriBundle' => 'Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle'));
        $container->setParameter('router.resource', array(
            'resource_type' => 'foo'
        ));

        $bundle = new HautelookTemplatedUriBundle();
        $bundle->build($container);

        $routerDef = new Definition('Symfony\Bundle\FrameworkBundle\Routing\Router');
        $routerDef->setArguments(array(new Reference('service_container'), array(), array()));
        $container->setDefinition('router.default', $routerDef);

        $container->setDefinition('parameter_bag', new Definition('Symfony\Component\DependencyInjection\Container'));
        $container->setDefinition('logger',  new Definition('Psr\Log\NullLogger'));
        $container->setDefinition('router.request_context',  new Definition('Symfony\Component\Routing\RequestContext'));

        $extension = $bundle->getContainerExtension();
        $extension->load($configs, $container);

        $container->getDefinition('hautelook.router.template')->setPublic(true);

        $container->compile();

        return $container;
    }

    public function testConfig()
    {
        $container = $this->getContainer();
        self::assertInstanceOf('Symfony\Bundle\FrameworkBundle\Routing\Router', $container->get('hautelook.router.template'));
    }
}
