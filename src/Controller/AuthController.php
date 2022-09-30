<?php

namespace Feierstoff\ToolboxBundle\Controller;

use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Parameter;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\Response;
use Feierstoff\ToolboxBundle\ApiGenerator\Attribute\RouteDescription;
use Feierstoff\ToolboxBundle\ApiGenerator\Parameter\Parameters;
use Feierstoff\ToolboxBundle\Auth\Authenticator;
use Feierstoff\ToolboxBundle\Exception\BadRequestException;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Exception\UnauthorizedException;
use Feierstoff\ToolboxBundle\Exception\ViolationException;
use Feierstoff\ToolboxBundle\Validator\Constraint;
use Feierstoff\ToolboxBundle\Validator\Constraint\NotBlank;
use Feierstoff\ToolboxBundle\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController {

    public function __construct(
        private readonly Authenticator $authenticator
    ) {}

    /**
     * @param Parameters $parameters
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws InternalServerException
     * @throws UnauthorizedException
     * @throws ViolationException
     */
    #[Route("/api/auth", methods: "POST")]
    #[RouteDescription("Handles the authorization for the webpage.")]
    #[Parameter("grant_type",
        type: Parameter::TYPE_STRING,
        required: true,
        constraints: [
            new Constraint\OneOf(["password", "refresh", "logout"])
        ],
        description: "
            Dadsd asdasd
        "
    )]
    #[Parameter("username",
        default: "",
        type: Parameter::TYPE_STRING,
        description: "
            Username for logging in a user. Required for grant_type 'password'.
        "
    )]
    #[Parameter("password",
        default: "",
        type: Parameter::TYPE_STRING,
        description: "
            Password for logging in a user. Required for grant_type 'password'.
        "
    )]
    #[Response(Response::OK, "User was logged in.", [
        "access_token" => "-1",
        "expires_in"
    ])]
    #[Response(Response::OK, "User was logged out.")]
    #[Response(Response::OK, "User authentification was refreshed.", [
        "access_token" => "-1",
        "expires_in"
    ])]
    public function index(Parameters $parameters, Request $request) {
        switch ($parameters->get("grant_type", "")) {
            case Authenticator::GRANT_TYPE_PASSWORD:
                $userdata = [
                    "username" => $parameters->get("username"),
                    "password" => $parameters->get("password")
                ];

                (new Validator($userdata))
                    ->add("username", [new NotBlank()])
                    ->add("password", [new NotBlank()])
                    ->violate();

                return $this->authenticator->loginResponse($request, $userdata["username"], $userdata["password"]);

            case Authenticator::GRANT_TYPE_REFRESH:
                return $this->authenticator->refreshResponse($request, "");

            case Authenticator::GRANT_TYPE_LOGOUT:
                return $this->authenticator->logoutResponse($request);

            default:
                throw new BadRequestException("Invalid parameter <grant_type>.");
        }
    }

}