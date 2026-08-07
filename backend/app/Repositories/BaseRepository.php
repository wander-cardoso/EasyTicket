<?php

namespace App\Repositories;

use PDO;

abstract class BaseRepository
{
    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    // Verifica se alguma linha foi afetada
    protected function sucesso(\PDOStatement $statement): bool
    {
        return $statement->rowCount() > 0;
    }
}