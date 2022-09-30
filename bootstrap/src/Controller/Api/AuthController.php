<?php

namespace App\Controller\Api;

use App\Entity\User;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Response;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\RouteDescription;
use Feierstoff\ToolboxBundle\Auth\Attribute\AuthNeeded;
use Feierstoff\ToolboxBundle\Controller\Controller;
use Feierstoff\ToolboxBundle\Response\OkResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends Controller {

    #[Route("/api/auth", methods: "GET")]
    #[RouteDescription("Returns the data of the currently authenticated user.")]
    #[AuthNeeded]
    #[Response(Response::OK, "Authentification is valid.", [
        "username" => "peter_peter",
        "privileges" => ["role:read"]
    ])]
    public function user(User $user) {
        return new OkResponse([
            "username" => $user->getUsername(),
            "privileges" => $user->getPrivileges()
        ]);
    }

}