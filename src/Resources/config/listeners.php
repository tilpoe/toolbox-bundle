<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Feierstoff\ToolboxBundle\ApiGenerator\ApiAttributeListener;
use Feierstoff\ToolboxBundle\ApiGenerator\Parameter\InjectParameters;
use Feierstoff\ToolboxBundle\Auth\Authenticator;
use Feierstoff\ToolboxBundle\Auth\Listener\AccessControlListener;
use Feierstoff\ToolboxBundle\EventListener\ExceptionListener;

return function(ContainerConfigurator $container) {
    $tag = "kernel.event_listener";
    $event_exception = "kernel.exception";
    $event_controller = "kernel.controller";

    $container->services()
        ->set(ExceptionListener::class)
            ->tag($tag, ["event" => $event_exception])
            ->tag("monolog.logger", ["channel" => "api"])
            ->arg("\$env", "%kernel.environment%")
            ->arg("\$mailer", service("mailer.default_transport"))

        ->set(ApiAttributeListener::class)
            ->tag($tag, ["event" => $event_controller])
            ->arg("\$injectParameters", service(InjectParameters::class))

        ->set(AccessControlListener::class)
            ->tag($tag, ["event" => $event_controller])
            ->arg("\$authenticator", service(Authenticator::class))
            ->arg("\$em", service("doctrine.orm.default_entity_manager"))
    ;
};