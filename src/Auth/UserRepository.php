<?php

namespace Feierstoff\ToolboxBundle\Auth;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRepository implements UserRepositoryInterface {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity) {
        $userObject = $this->getUserEntity($username);

        if (!$userObject || !$this->passwordHasher->isPasswordValid($userObject, $password)) {
            return null;
        }

        return $userObject;
    }

    public function getUserEntity($username) {
        $conn = $this->em->getConnection();
        $sql = "
            SELECT u.id, u.password, r.privileges
            FROM `user` u
            LEFT JOIN `role` r ON u.role_id = r.id
            WHERE `username` = :username
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            "username" => $username
        ]);
        $user = $result->fetchAllAssociative();

        if (count($user) !== 1) {
            return null;
        }

        return new User($user[0]["id"], $user[0]["password"], json_decode($user[0]["privileges"], true) ?? []);

    }

}

