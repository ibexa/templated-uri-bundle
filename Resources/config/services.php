<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableCompiledUrlMatcher;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('hautelook.router.template.generator.class', Rfc6570Generator::class);

    $container->services()
        ->set('hautelook.router.template', Router::class)
            ->args([
                service('service_container'),
                param('router.resource'),
                [
                    'cache_dir' => param('kernel.cache_dir'),
                    'debug' => param('kernel.debug'),
                    'generator_class' => param('hautelook.router.template.generator.class'),
                    'generator_dumper_class' => CompiledUrlGeneratorDumper::class,
                    'matcher_class' => RedirectableCompiledUrlMatcher::class,
                    'matcher_dumper_class' => CompiledUrlMatcherDumper::class,
                ],
                service('router.request_context')->ignoreOnInvalid(),
                service('parameter_bag')->ignoreOnInvalid(),
                service('logger')->ignoreOnInvalid(),
                param('kernel.default_locale'),
            ])
            ->tag('fsc_hateoas.url_generator', ['alias' => 'templated'])
            ->tag('hateoas.url_generator', ['alias' => 'templated_uri'])
            ->tag('monolog.logger', ['channel' => 'router']);
};
