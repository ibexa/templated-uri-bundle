<?php

namespace Hautelook\TemplatedUriBundle\Tests\DependencyInjection;

use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContainerTest extends TestCase
{
    private function getContainer(array $configs = array()): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.environment', 'test');
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.default_locale', 'en');
        $container->setParameter('kernel.cache_dir', tempnam(sys_get_temp_dir(), "HautelookTemplatedUriBundle"));
        $container->setParameter('kernel.bundles', array('HautelookTemplatedUriBundle' => 'Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle'));
        $container->setParameter('router.resource', 'routing.yml');

        $bundle = new HautelookTemplatedUriBundle();
        $bundle->build($container);

        // Fakes the application's router.default, whose 3rd constructor argument
        // (the options array) carries resource_type/strict_requirements resolved
        // from the app configuration. TemplatedRouterPass copies those onto
        // hautelook.router.template.
        $routerDef = new Definition(Router::class);
        $routerDef->setArguments(array(
            new Reference('service_container'),
            'routing.yml',
            array('resource_type' => 'foo'),
        ));
        $container->setDefinition('router.default', $routerDef);

        $container->setDefinition('parameter_bag', new Definition('Symfony\Component\DependencyInjection\Container'));
        $container->setDefinition('logger', new Definition('Psr\Log\NullLogger'));
        $container->setDefinition('router.request_context', new Definition('Symfony\Component\Routing\RequestContext'));

        $extension = $bundle->getContainerExtension();
        $extension->load($configs, $container);

        $container->getDefinition('hautelook.router.template')->setPublic(true);

        $container->compile();

        return $container;
    }

    public function testConfig(): void
    {
        $container = $this->getContainer();
        self::assertInstanceOf(Router::class, $container->get('hautelook.router.template'));
    }

    public function testResourceTypeIsCopiedFromRouterDefault(): void
    {
        $container = $this->getContainer();

        $options = $container->getDefinition('hautelook.router.template')->getArgument(2);

        self::assertSame('foo', $options['resource_type']);
        self::assertSame(Rfc6570Generator::class, $options['generator_class']);
    }

    public function testTemplatedRouterUsesRfc6570Generator(): void
    {
        $container = $this->getContainer();

        /** @var Router $router */
        $router = $container->get('hautelook.router.template');

        self::assertSame(Rfc6570Generator::class, $router->getOption('generator_class'));
    }
}
