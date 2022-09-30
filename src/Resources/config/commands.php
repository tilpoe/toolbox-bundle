<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Feierstoff\ToolboxBundle\Command\BootstrapCommand;
use Feierstoff\ToolboxBundle\Command\BootstrapWebapp;
use Feierstoff\ToolboxBundle\Command\PrepareBuildCommand;

return function(ContainerConfigurator $container) {
    $tag = "console.command";

    $container->services()
        ->set(BootstrapCommand::class)
        ->tag($tag)
        ->set(PrepareBuildCommand::class)
        ->tag($tag)
    ;
};