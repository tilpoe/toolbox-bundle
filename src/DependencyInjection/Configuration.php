<?php

namespace Feierstoff\ToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        // name: parent key in config/toolbox.yaml file
        $treeBuilder = new TreeBuilder("toolbox");

        // define config tree
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode("user_entity")->defaultValue("App\Entity\User")->end()
                ->scalarNode("twig_fallback_path")->defaultValue("app.html.twig")->end()
                ->scalarNode("auth_method")
                    ->defaultValue("session")
                    ->validate()
                        ->ifNotInArray(["oauth", "session"])
                        ->thenInvalid("Invalid auth method.")
                    ->end()
                ->end()
                ->arrayNode("mailer")
                    ->isRequired()
                    ->children()
                        ->arrayNode("sender")
                            ->isRequired()
                            ->children()
                                ->scalarNode("mail")->isRequired()->end()
                                ->scalarNode("name")->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode("send")
                            ->children()
                                ->scalarNode("exception")->defaultValue(null)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }

}