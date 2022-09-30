<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Feierstoff\ToolboxBundle\EntityInterface\UserEntityInterface;
use Feierstoff\ToolboxBundle\Serializer\Attribute\Ignore;

#[ORM\Entity]
class User implements UserEntityInterface, \Symfony\Component\Security\Core\User\UserInterface {
    #[Ignore]
    public function getRoles(): array {}
    #[Ignore]
    public function eraseCredentials(){}
    #[Ignore]
    public function getUserIdentifier(): string {}

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected int $id;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    protected string $username;

    #[ORM\Column(type: "string")]
    protected string $password;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: "users")]
    protected ?Role $role = null;

    public function __construct() {

    }

    public function getId(): int {
        return $this->id;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function getUsername(): string {
        return $this->username;
    }

    #[Ignore]
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getRole(): ?Role {
        return $this->role;
    }

    public function setRole(?Role $role): self {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getPrivileges(): array {
        return $this->getRole()?->getPrivileges() ?? [];
    }

    public function hasPrivilege(string $privilege): bool {
        if ($this->getRole()) {
            foreach ($this->getRole()->getPrivileges() as $foundPrivilege) {
                if ($privilege == $foundPrivilege) {
                    return true;
                }
            }
        }

        return false;
    }
}