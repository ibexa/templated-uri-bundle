<?php

namespace Hautelook\TemplatedUriBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Copies the router options that depend on application configuration
 * (resource_type, strict_requirements) from the default router onto the templated one.
 */
class TemplatedRouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $router = $container->findDefinition('router.default');
        $resourceOptions = $router->getArgument(2);

        $templatedRouter = $container->findDefinition('hautelook.router.template');
        $templatedResourceOptions = $templatedRouter->getArgument(2);

        foreach (['resource_type', 'strict_requirements'] as $option) {
            if (isset($resourceOptions[$option])) {
                $templatedResourceOptions[$option] = $resourceOptions[$option];
            }
        }

        $templatedRouter->replaceArgument(2, $templatedResourceOptions);
    }
}
