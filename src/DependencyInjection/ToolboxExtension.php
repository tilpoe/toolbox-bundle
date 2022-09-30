<?php

namespace Feierstoff\ToolboxBundle\DependencyInjection;

use Feierstoff\ToolboxBundle\Auth\Authenticator;
use Feierstoff\ToolboxBundle\Auth\Listener\AccessControlListener;
use Feierstoff\ToolboxBundle\Controller\ApiDocController;
use Feierstoff\ToolboxBundle\Controller\FallbackController;
use Feierstoff\ToolboxBundle\Route\FallbackRoute;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class ToolboxExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__."/../Resources/config"));
        $configuration = $this->getConfiguration($configs, $container);
        // array one can access to get the values from the config file
        $config = $this->processConfiguration($configuration, $configs);

        /* --- CONTROLLERS --- */
        $loader->load("controllers.php");

        $fallbackRoute = $container->getDefinition(FallbackController::class);
        $fallbackRoute->setArgument("\$conf_twig_fallback_path", $config["twig_fallback_path"]);


        /* --- LISTENERS --- */
        $loader->load("listeners.php");
        $accessControlListener = $container->getDefinition(AccessControlListener::class);
        $accessControlListener->setArgument("\$conf_user_entity", $config["user_entity"]);



        /* --- COMMANDS --- */
        $loader->load("commands.php");



        /* --- SERVICES --- */
        $loader->load("services.php");

        $authenticator = $container->getDefinition(Authenticator::class);
        $authenticator->setArgument("\$conf_auth_method", $config["auth_method"]);


    }

}