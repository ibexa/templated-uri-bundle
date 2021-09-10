<?php

namespace Hautelook\TemplatedUriBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TemplatedRouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $router = $container->findDefinition('router.default');
        $resourceOptions = $router->getArgument(2);

        $templatedRouter = $container->findDefinition('hautelook.router.template');
        $templatedResourceOptions = $templatedRouter->getArgument(2);
        if (isset($resourceOptions['resource_type'])) {
            $templatedResourceOptions['resource_type'] = $resourceOptions['resource_type'];
        }
        if (isset($resourceOptions['strict_requirements'])) {
            $templatedResourceOptions['strict_requirements'] = $resourceOptions['strict_requirements'];
        }

        $legacyGenerator = !is_a(
            'Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator',
            'Symfony\Component\Routing\Generator\CompiledUrlGenerator',
            true
        );

        // Symfony 4 and 5 no longer uses those argument thus we add them conditionally for older Symfony versions
        if ($legacyGenerator) {
            $templatedResourceOptions['generator_base_class'] = '%hautelook.router.template.generator.class%';
            $templatedResourceOptions['generator_cache_class'] = '%kernel.name%%kernel.environment%RF6570UrlGenerator';
            $templatedResourceOptions['matcher_base_class'] = 'Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher';
            $templatedResourceOptions['matcher_cache_class'] = '%kernel.name%%kernel.environment%RFC6570UrlMatcher';
        } else {
            $templatedResourceOptions['generator_dumper_class'] = 'Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper';
        }

        $templatedRouter->replaceArgument(2, $templatedResourceOptions);

        $ref = new \ReflectionClass($templatedRouter->getClass());
        $cArgs = $ref->getConstructor()->getParameters();
        if (count($cArgs) < 5) { // Symfony < 4
            $args = $templatedRouter->getArguments();
            $args = array_slice($args, 0, 4);
            $templatedRouter->setArguments($args);
        }
    }
}

