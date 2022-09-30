<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    /* --- FALLBACK --- */
/*    $routes
        ->add("feierstoff_toolbox.route.fallback.index", "/")
        ->controller([\Feierstoff\ToolboxBundle\Route\FallbackRoute::class, "index"])
    ;*/

    /* --- AUTH --- */
/*    $routes
        ->add("feierstoff_toolbox.route.auth.index", "/api/auth")
        ->controller([\Feierstoff\ToolboxBundle\Route\AuthRoute::class, "index"])
        ->methods(["POST"])
    ;*/
};