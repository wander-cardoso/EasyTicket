<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    //Conexão única (Singleton)

    private static ?PDO $connection = null;

    // Esse metodo retorna uma conexão PDO
    public static function getConnection(): PDO
    {
        // Se a conexão já existir, reutilizamos.
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {

            // Monta a string de conexão (DSN)
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_DATABASE']
            );

            // Cria a conexão com o banco

            self::$connection = new PDO(
                $dsn,
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );

            // Lança exceções quando ocorrer erro.
            self::$connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            // Retorna resultados como array associativo.
            self::$connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return self::$connection;

        } catch (PDOException $exception) {

            throw new PDOException(
                'Erro ao conectar ao banco de dados: ' . $exception->getMessage()
            );

        }
    }
}