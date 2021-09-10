<?php

namespace Hautelook\TemplatedUriBundle;

use Hautelook\TemplatedUriBundle\DependencyInjection\CompilerPass\TemplatedRouterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HautelookTemplatedUriBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TemplatedRouterPass());
    }
}
