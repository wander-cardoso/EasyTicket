<?php

namespace App\Repositories;

use App\Exceptions\DatabaseException;
use App\Interfaces\Repositories\UtilizadorRepositoryInterface;
use App\Models\Utilizador;
use PDOException;
// Responsável pelo acesso aos dados dos utilizadores
class UtilizadorRepository extends BaseRepository implements UtilizadorRepositoryInterface
{
    // Busca um utilizador pelo nome_utilizador para LOGIN
    public function buscarPorNomeUtilizador(string $nome_utilizador): ?Utilizador
    {
        try {
            // SQL responsável por buscar o utilizador pelo nome_utilizador
            $sql = "
                SELECT
                    id,
                    nome,
                    nome_utilizador,
                    password,
                    perfil,
                    criado_em
                FROM utilizadores
                WHERE nome_utilizador = :nome_utilizador
                LIMIT 1
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa a consulta
            $statement->execute([
                ':nome_utilizador' => $nome_utilizador
            ]);

            // Obtém os dados do utilizador
            $dados = $statement->fetch();

            // Utilizador não encontrado
            if ($dados === false) { return null; }

            // Converte os dados do banco para o Model
            return new Utilizador(
                (int) $dados['id'],
                $dados['nome'],
                $dados['nome_utilizador'],
                $dados['password'],
                $dados['perfil'],
                $dados['criado_em']
            );

        } catch (PDOException $exception) {
            throw new DatabaseException(
                'Erro ao consultar o utilizador.', 0,
                $exception
            );
        }
    }

    
            // Cria um novo utilizador
public function criar(Utilizador $utilizador): Utilizador
{
    try {
        // SQL responsável por criar o utilizador
        $sql = "
            INSERT INTO utilizadores (
                nome,
                nome_utilizador,
                password,
                perfil
            ) VALUES (
                :nome,
                :nome_utilizador,
                :password,
                :perfil
            )
        ";

        // Prepara a query
        $statement = $this->connection->prepare($sql);

        // Executa o INSERT
        $statement->execute([
            ':nome' => $utilizador->getNome(),
            ':nome_utilizador' => $utilizador->getNomeUtilizador(),
            ':password' => $utilizador->getPassword(),
            ':perfil' => $utilizador->getPerfil()
        ]);

        // Obtém o ID criado pelo banco
        $id = (int) $this->connection->lastInsertId();

        // Retorna o utilizador criado
        return new Utilizador(
            $id,
            $utilizador->getNome(),
            $utilizador->getNomeUtilizador(),
            $utilizador->getPassword(),
            $utilizador->getPerfil(),
            date('Y-m-d H:i:s')
        );

    } catch (PDOException $exception) {
        throw new DatabaseException(
            'Erro ao criar o utilizador.',
            0,
            $exception
        );
    }
}

// Lista todos os utilizadores
public function listar(): array
{
    try {
        $sql = "
            SELECT
                id,
                nome,
                nome_utilizador,
                password,
                perfil,
                criado_em
            FROM utilizadores
            ORDER BY nome ASC
        ";

        $statement = $this->connection->prepare($sql);

        $statement->execute();

        $dados = $statement->fetchAll();

        $utilizadores = [];

        foreach ($dados as $dado) {
            $utilizadores[] = new Utilizador(
                (int) $dado['id'],
                $dado['nome'],
                $dado['nome_utilizador'],
                $dado['password'],
                $dado['perfil'],
                $dado['criado_em']
            );
        }

        return $utilizadores;

    } catch (PDOException $exception) {
        throw new DatabaseException(
            'Erro ao listar os utilizadores.',
            0,
            $exception
        );
    }
}

// Consulta um utilizador pelo ID
public function consultar(int $id): ?Utilizador
{
    try {
        $sql = "
            SELECT
                id,
                nome,
                nome_utilizador,
                password,
                perfil,
                criado_em
            FROM utilizadores
            WHERE id = :id
            LIMIT 1
        ";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            ':id' => $id
        ]);

        $dados = $statement->fetch();

        if ($dados === false) {
            return null;
        }

        return new Utilizador(
            (int) $dados['id'],
            $dados['nome'],
            $dados['nome_utilizador'],
            $dados['password'],
            $dados['perfil'],
            $dados['criado_em']
        );

    } catch (PDOException $exception) {
        throw new DatabaseException(
            'Erro ao consultar o utilizador.',
            0,
            $exception
        );
    }
}

// Edita um utilizador existente
public function editar(Utilizador $utilizador): Utilizador
{
    try {
        $sql = "
            UPDATE utilizadores
            SET
                nome = :nome,
                nome_utilizador = :nome_utilizador,
                password = :password,
                perfil = :perfil
            WHERE id = :id
        ";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            ':nome' => $utilizador->getNome(),
            ':nome_utilizador' => $utilizador->getNomeUtilizador(),
            ':password' => $utilizador->getPassword(),
            ':perfil' => $utilizador->getPerfil(),
            ':id' => $utilizador->getId()
        ]);

        return $utilizador;

    } catch (PDOException $exception) {
        throw new DatabaseException(
            'Erro ao editar o utilizador.',
            0,
            $exception
        );
    }
}

}