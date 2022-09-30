<?php

namespace Feierstoff\ToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        // name: parent key in config/feierstoff_toolbox.yaml file
        $treeBuilder = new TreeBuilder("feierstoff_toolbox");

        // define config tree
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode("user_entity")
                    ->defaultValue("App\Entity\User")
                ->end()

                ->scalarNode("twig_fallback_path")
                    ->defaultValue("app.html.twig")
                ->end()

                ->scalarNode("auth_method")
                    ->defaultValue("session")
                    ->validate()
                        ->ifNotInArray(["oauth", "session"])
                        ->thenInvalid("Invalid auth method.")
                    ->end()
                ->end()

        ;

        return $treeBuilder;
    }

}