<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Feierstoff\ToolboxBundle\ApiGenerator\ApiDocGenerator;
use Feierstoff\ToolboxBundle\Auth\Authenticator;
use Feierstoff\ToolboxBundle\Controller\ApiDocController;
use Feierstoff\ToolboxBundle\Controller\AuthController;
use Feierstoff\ToolboxBundle\Controller\FallbackController;

return function(ContainerConfigurator $container) {

    $container->services()
        ->set(FallbackController::class)
            ->tag("controller.service_arguments")
            ->arg(0, service("twig"))
            ->alias("feierstoff_toolbox.route.fallback", FallbackController::class)
            ->public()

        ->set(AuthController::class)
            ->tag("controller.service_arguments")
            ->arg("\$authenticator", service(Authenticator::class))
            ->alias("feierstoff_toolbox.route.auth", AuthController::class)
            ->public()

        ->set(ApiDocController::class)
            ->tag("controller.service_arguments")
            ->arg("\$twig", service("twig"))
            ->arg("\$apiDocGenerator", service(ApiDocGenerator::class))
            ->alias("feierstoff_toolbox.route.api_doc", ApiDocController::class)
            ->public()
    ;

};