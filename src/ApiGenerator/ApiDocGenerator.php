<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator;

use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Parameter;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Response;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\RouteDescription;
use Feierstoff\ToolboxBundle\Auth\Attribute\HasPrivilege;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Route;

class ApiDocGenerator {

    public function __construct(
        private Router $router
    ) {}

    public function generate(): array {
        $routeCollection = [];
        $routes = $this->router->getRouteCollection();
        /** @var Route $route */
        foreach ($routes as $route) {
            $path = $route->getPath();
            if (!str_starts_with($path, "/api/")) continue;

            $controller = explode("::", $route->getDefault("_controller"))[0];
            $endpoint = explode("/", $path)[2];

            $method = $route->getMethods()[0];

            $routeDefinition = [
                "path" => $path,
                "method" => $method,
                "parameters" => [],
                "responses" => [],
                "privileges" => [],
                "description" => "No description available.",
                "classes" => [
                    "method" => "bg-method-{$method}",
                    "border-top" => "bg-border-top-{$method}",
                    "border" => "bg-border-{$method}",
                    "bg" => "bg-{$method}"
                ]
            ];

            $reflection = new \ReflectionMethod($route->getDefault("_controller"));
            $routeDescription = $reflection->getAttributes(RouteDescription::class);
            if (sizeof($routeDescription) > 0) {
                foreach ($routeDescription as $description) {
                    $description = $description->newInstance();
                    if ($description instanceof RouteDescription) {
                        $routeDefinition["description"] = $description->getDescription();
                    }
                    break;
                }
            }

            // parse parameters
            $parameters = $reflection->getAttributes(Parameter::class);
            foreach ($parameters as $parameter) {
                $parameter = $parameter->newInstance();
                $oneOf = $parameter->getOneOfForDocs();
                if ($parameter instanceof Parameter) {
                    $routeDefinition["parameters"][] = [
                        "name" => $parameter->getKey(),
                        "type" => $parameter->getType() == "" ? "undefined" : $parameter->getType(),
                        "required" => $parameter->getRequired(),
                        "description" => $parameter->getDescription(),
                        "default" => $parameter->getDefaultForDocs(),
                        "oneOf" => $oneOf ? implode(", ", $oneOf) : null
                    ];
                }
            }

            // parse response
            $responses = $reflection->getAttributes(Response::class);
            foreach ($responses as $response) {
                $response = $response->newInstance();
                if ($response instanceof Response) {
                    $routeDefinition["responses"][] = [
                        "code" => $response->getCode(),
                        "description" => $response->getDescription(),
                        "example" => $response->getExample()
                    ];
                }
            }

            // parse privileges
            $privileges = $reflection->getAttributes(HasPrivilege::class);
            foreach ($privileges as $privilege) {
                $privilege = $privilege->newInstance();
                if ($privilege instanceof HasPrivilege) {
                    $routeDefinition["privileges"][] = $privilege->get();
                }
            }

            if (array_key_exists($endpoint, $routeCollection)) {
                $routeCollection[$endpoint][] = $routeDefinition;
            } else {
                $routeCollection[$endpoint] = [$routeDefinition];
            }
        }

        return $routeCollection;
    }

}