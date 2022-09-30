<?php

namespace Feierstoff\ToolboxBundle\EntityInterface;

interface UserEntityInterface {

    public function getId(): int;
    public function getUsername(): string;
    public function getPrivileges(): array;
    public function hasPrivilege(string $privilege): bool;

}