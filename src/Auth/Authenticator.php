<?php

namespace Feierstoff\ToolboxBundle\Auth;

use Doctrine\ORM\EntityManagerInterface;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Exception\UnauthorizedException;
use Feierstoff\ToolboxBundle\Helper\DateTime;
use Feierstoff\ToolboxBundle\Response\OkResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Authenticator {

    public const GRANT_TYPE_PASSWORD = "password";
    public const GRANT_TYPE_REFRESH = "refresh";
    public const GRANT_TYPE_LOGOUT = "logout";

    public const AUTH_METHOD_OAUTH = "oauth";
    public const AUTH_METHOD_SESSION = "session";

    private UserRepository $userRepository;
    private ClientRepository $clientRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly string $conf_auth_method
    ) {
        $this->userRepository = new UserRepository($this->em, $this->passwordHasher);
        $this->clientRepository = new ClientRepository();
    }

    /**
     * @param Request $request
     * @param string $username
     * @param string $password
     * @return JsonResponse
     * @throws UnauthorizedException
     */
    public function loginResponse(Request $request, string $username, string $password): JsonResponse {
        return $this->handleAuth($request, [
            "username" => $username,
            "password" => $password,
            "grant_type"=> self::GRANT_TYPE_PASSWORD
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws InternalServerException
     */
    public function logoutResponse(Request $request): JsonResponse {
        $response = new OkResponse();

        switch ($this->conf_auth_method) {
            case self::AUTH_METHOD_SESSION:
                $request->getSession()->remove("auth");
                return $response;

            default:
                throw new InternalServerException();
        }
    }

    public function refreshResponse(Request $request): JsonResponse {
        $refreshToken = "";

        return $this->handleAuth($request, [
           "refresh_token" => $refreshToken,
           "grant_type" => self::GRANT_TYPE_REFRESH
        ]);
    }

    /**
     * @param Request $request
     * @param array $params
     * @return JsonResponse
     * @throws UnauthorizedException
     */
    private function handleAuth(Request $request, array $params): JsonResponse {
        switch ($this->conf_auth_method) {
            case self::AUTH_METHOD_SESSION:
                switch ($params["grant_type"]) {
                    case self::GRANT_TYPE_PASSWORD:
                        $user = ($this->userRepository->getUserEntityByUserCredentials($params["username"], $params["password"], $params["grant_type"], $this->clientRepository->getClientEntity("client")));

                        if ($user) {
                            $request->getSession()->set("auth", [
                                "timestamp" => DateTime::createImmutable(),
                                "userId" => $user->getIdentifier(),
                                "user" => $user
                            ]);
                            return new OkResponse([
                                "accessToken" => "success",
                                "expiresIn" => -1,
                                "privileges" => $user->getPrivileges()
                            ]);
                        }
                        break;

                    case self::GRANT_TYPE_REFRESH:
                        $userData = $request->getSession()->get("auth", null);

                        if ($userData && $userData["timestamp"]->modify("+1 day") >= DateTime::createImmutable()) {
                            $userObject = $this->userRepository->getUserEntity($userData["user"]->getUserIdentifier());

                            if ($userObject) {
                                $request->getSession()->set("auth", [
                                    "timestamp" => DateTime::createImmutable(),
                                    "userId" => $userObject->getIdentifier(),
                                    "user" => $userObject
                                ]);
                                return new OkResponse([
                                    "accessToken" => "success",
                                    "expiresIn" => -1,
                                    "privileges" => $userObject->getPrivileges()
                                ]);
                            }
                        }
                        break;
                }
                break;

            default:
                throw new UnauthorizedException();
        }

        throw new UnauthorizedException();
    }

    /**
     * @param Request $request
     * @return int
     * @throws UnauthorizedException
     */
    public function validateRequest(Request $request): int {
        switch ($this->conf_auth_method) {
            case self::AUTH_METHOD_SESSION:
                if (($userData = $request->getSession()->get("auth", null)) !== null) {
                    return $userData["userId"];
                }
                break;
        }

        throw new UnauthorizedException();
    }

}