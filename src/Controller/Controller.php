<?php

namespace Feierstoff\ToolboxBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Feierstoff\ToolboxBundle\EntityInterface\UserEntityInterface;
use Feierstoff\ToolboxBundle\Exception\InternalServerException;
use Feierstoff\ToolboxBundle\Response\ExceptionResponse;
use Feierstoff\ToolboxBundle\Serializer\Serializer;
use Feierstoff\ToolboxBundle\Util\SymfonyParameter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class Controller extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController {

    private ?ManagerRegistry $managerRegistry;

    private ?Serializer $serializer;

    private ?SessionInterface $session;

    #[Required]
    public function setEm(ManagerRegistry $managerRegistry) {
        $this->managerRegistry = $managerRegistry;
    }

    protected function Em(?string $connection = "default"): ObjectManager {
        $em = $this->managerRegistry->getManager($connection);

        if (!$em) {
            throw new InternalServerException("No entity manager found for connection {$connection}.");
        }

        return $em;
    }

    protected function Qb(?string $connection = "default"): QueryBuilder {
        return $this->managerRegistry->getManager($connection)->createQueryBuilder();
    }

    protected function Connection(?string $connection = "default"): Connection {
        return $this->managerRegistry->getConnection($connection);
    }

    #[Required]
    public function setSerializer(Serializer $serializer) {
        $this->serializer = $serializer;
    }

    protected function Serializer(): Serializer {
        return $this->serializer;
    }

    #[Required]
    public function setSession(RequestStack $requestStack) {
        $this->session = $requestStack->getSession();
    }

    public function Session(): SessionInterface {
        return $this->session;
    }

    protected function isDevEnv(): bool {
        return $this->getParameter(SymfonyParameter::ENV) === "dev";
    }

    protected function isProdEnv(): bool {
        return $this->getParameter(SymfonyParameter::ENV) === "prod";
    }

    protected function isTestEnv(): bool {
        return $this->getParameter(SymfonyParameter::ENV) === "test";
    }

    protected function isEnv(string $env): bool {
        return $this->getParameter(SymfonyParameter::ENV) === $env;
    }

    protected function getProjectDir(): string {
        return $this->getParameter(SymfonyParameter::ROOT_DIR);
    }
}