<?php

namespace Feierstoff\ToolboxBundle\Controller;

use Feierstoff\ToolboxBundle\ApiGenerator\ApiDocGenerator;
use Feierstoff\ToolboxBundle\Auth\Attribute\HasPrivilege;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ApiDocController {

    public function __construct(
        private readonly Environment $twig,
        private readonly ApiDocGenerator $apiDocGenerator
    ) {}

    #[Route("/api")]
    #[HasPrivilege("api")]
    public function index(Request $request): Response {
        $routes = $this->apiDocGenerator->generate();

        return new Response($this->twig->render("@Toolbox/api-doc.html.twig", [
            "endpoints" => $routes
        ]));
    }

}