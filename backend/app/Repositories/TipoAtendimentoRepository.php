<?php

namespace App\Repositories;

use PDO;
use PDOException;
use App\Models\TipoAtendimento;
use App\Exceptions\DatabaseException;
use App\Interfaces\Repositories\TipoAtendimentoRepositoryInterface;

// Classe que manipula banco de dados na tabela tipos de Atendimento
class TipoAtendimentoRepository extends BaseRepository implements TipoAtendimentoRepositoryInterface
{
    public function listar(): array
    {
        try {

            // SQL responsável por buscar todos os tipos de atendimento
            $sql = "
                SELECT
                    id,
                    nome,
                    sigla,
                    descricao
                FROM tipos_atendimento
                ORDER BY nome
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT.
            $statement->execute();

            // Obtém todos os registos como array associativo
            $registos = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Lista que será devolvida ao Service
            $tipos_atendimento = [];

            // Converte cada registo em um objeto
            foreach ($registos as $registo) {

                $tipo_atendimento = new TipoAtendimento(
                    $registo['id'],
                    $registo['nome'],
                    $registo['sigla'],
                    $registo['descricao']
                );

                $tipos_atendimento[] = $tipo_atendimento;
            }

            return $tipos_atendimento;

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao listar os tipos de atendimento.',
                0,
                $exception
            );
        }
    }

    public function criar(TipoAtendimento $tipoAtendimento): bool
    {
        try {

            // SQL responsável por inserir um novo tipo de atendimento.
            $sql = "
                INSERT INTO tipos_atendimento (
                    nome,
                    sigla,
                    descricao
                )
                VALUES (
                    :nome,
                    :sigla,
                    :descricao
                )
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o INSERT
            $statement->execute([
                ':nome' => $tipoAtendimento->getNome(),
                ':sigla' => $tipoAtendimento->getSigla(),
                ':descricao' => $tipoAtendimento->getDescricao()
            ]);

            return $this->sucesso($statement);

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao criar o tipo de atendimento.',
                0,
                $exception
            );
        }
    }

    public function atualizar(
        int $id,
        TipoAtendimento $tipoAtendimento
    ): bool
    {
        try {

            // SQL responsável por atualizar um tipo de atendimento
            $sql = "
                UPDATE tipos_atendimento
                SET
                    nome = :nome,
                    sigla = :sigla,
                    descricao = :descricao
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o UPDATE
            $statement->execute([
                ':id' => $id,
                ':nome' => $tipoAtendimento->getNome(),
                ':sigla' => $tipoAtendimento->getSigla(),
                ':descricao' => $tipoAtendimento->getDescricao()
            ]);

            // Retorna verdadeiro caso algum registro tenha sido atualizado
            return $this->sucesso($statement);

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao atualizar o tipo de atendimento.',
                0,
                $exception
            );
        }
    }

    public function excluir(int $id): bool
    {
        try {

            // SQL responsável por excluir um tipo de atendimento.
            $sql = "
                DELETE FROM tipos_atendimento
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o DELETE.
            $statement->execute([
                ':id' => $id
            ]);

            // Retorna verdadeiro caso algum registro tenha sido excluído
            return $this->sucesso($statement);

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao excluir o tipo de atendimento.',
                0,
                $exception
            );
        }
    }
}