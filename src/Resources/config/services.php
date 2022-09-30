<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Feierstoff\ToolboxBundle\ApiGenerator\ApiDocGenerator;
use Feierstoff\ToolboxBundle\ApiGenerator\Parameter\InjectParameters;
use Feierstoff\ToolboxBundle\Auth\Authenticator;
use Feierstoff\ToolboxBundle\Repository\Repository;
use Feierstoff\ToolboxBundle\Serializer\Serializer;

return function(ContainerConfigurator $container) {

    $container->services()
        ->set(Authenticator::class)
            ->arg("\$em", service("doctrine.orm.default_entity_manager"))
            ->arg("\$passwordHasher", service("security.user_password_hasher"))
            ->alias("feierstoff_toolbox.service.authenticator", Authenticator::class)

        ->set(Serializer::class)
            ->alias("feierstoff_toolbox.service.serializer", Serializer::class)

        ->set(InjectParameters::class)
            ->alias("feierstoff_toolbox.service.inject_parameters", InjectParameters::class)

        ->set(ApiDocGenerator::class)
            ->arg("\$router", service("router.default"))
            ->alias("feierstoff_toolbox.service.api_doc_generator", ApiDocGenerator::class)

        ->set(Repository::class)
            ->arg("\$managerRegistry", service("doctrine"))
            ->alias("feierstoff_toolbox.service.repository", Repository::class)
    ;

};