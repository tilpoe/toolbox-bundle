<?php

namespace Feierstoff\ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FallbackController {

    public function __construct(
        private readonly Environment $twig,
        private readonly string $conf_twig_fallback_path
    ) {}

    #[Route("/")]
    public function index(): Response {
        return new Response($this->twig->render($this->conf_twig_fallback_path));
    }

}