<?php

namespace Feierstoff\ToolboxBundle\Auth;

use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserEntityInterface, PasswordAuthenticatedUserInterface {

    /**
     * @param string $userId
     * @param string $password
     * @param string[] $privileges
     */
    public function __construct (
        private readonly string $userId,
        private readonly string $password,
        private readonly array $privileges = []
    ) {}

    public function getIdentifier(): string {
        return $this->userId;
    }

    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @return string[]
     */
    public function getPrivileges(): array {
        return $this->privileges;
    }

    public function getRoles(): array { return []; }
    public function eraseCredentials(){}
    public function getUserIdentifier(): string{ return ""; }
}