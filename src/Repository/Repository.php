<?php

namespace Feierstoff\ToolboxBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class Repository {

    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {

    }

    /**
     * @param string|null $connection
     * @return Connection
     */
    protected function connection(?string $connection = "default"): object {
        return $this->managerRegistry->getConnection($connection);
    }

    protected function qb(?string $connection = "default"): QueryBuilder {
        return $this->managerRegistry->getManager($connection)->createQueryBuilder();
    }

}