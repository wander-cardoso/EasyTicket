<?php

namespace App\Repositories;

use PDO;
use PDOException;
use App\Models\Balcao;
use App\Exceptions\DatabaseException;
use App\Interfaces\Repositories\BalcaoRepositoryInterface;

// Classe que manipula banco de dados na tabela Balcões
class BalcaoRepository extends BaseRepository implements BalcaoRepositoryInterface
{
    public function listar(): array
    {
        try {

            // SQL responsável por buscar todos os balcões
            $sql = "
                SELECT
                    id,
                    numero,
                    nome,
                    tipo_atendimento_id
                FROM balcoes
                ORDER BY numero
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute();

            // Obtém todos os registos como array associativo
            $registos = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Lista que será devolvida ao Service
            $balcoes = [];

            // Converte cada registo em um objeto
            foreach ($registos as $registo) {

                $balcoes[] = new Balcao(
                    $registo['id'],
                    $registo['numero'],
                    $registo['nome'],
                    $registo['tipo_atendimento_id']
                );
            }

            return $balcoes;

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao listar os balcões.',
                0,
                $exception
            );
        }
    }

    // Verifica se o nome do balcão já existe
public function nomeExiste(
    string $nome,
    ?int $id = null
): bool {
    try {

        // SQL responsável por verificar se o nome já está cadastrado
        $sql = "
            SELECT COUNT(*)
            FROM balcoes
            WHERE nome = :nome
        ";

        // Se estiver atualizando, ignora o próprio balcão
        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        // Prepara a query
        $statement = $this->connection->prepare($sql);

        // Define os parâmetros da consulta
        $parametros = [
            ':nome' => $nome
        ];

        if ($id !== null) {
            $parametros[':id'] = $id;
        }

        // Executa o SELECT
        $statement->execute($parametros);

        // Obtém a quantidade de registros encontrados
        $quantidade = (int) $statement->fetchColumn();

        return $quantidade > 0;

    } catch (PDOException $exception) {

        throw new DatabaseException(
            'Erro ao verificar o nome do balcão.',
            0,
            $exception
        );
    }
}
// Verifica se o número do balcão já existe
public function numeroExiste(
    int $numero,
    ?int $id = null
): bool {
    try {

        // SQL responsável por verificar se o número já está cadastrado
        $sql = "
            SELECT COUNT(*)
            FROM balcoes
            WHERE numero = :numero
        ";

        // Se estiver atualizando, ignora o próprio balcão
        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        // Prepara a query
        $statement = $this->connection->prepare($sql);

        // Define os parâmetros da consulta
        $parametros = [
            ':numero' => $numero
        ];

        if ($id !== null) {
            $parametros[':id'] = $id;
        }

        // Executa o SELECT
        $statement->execute($parametros);

        // Obtém a quantidade de registros encontrados
        $quantidade = (int) $statement->fetchColumn();

        return $quantidade > 0;

    } catch (PDOException $exception) {

        throw new DatabaseException(
            'Erro ao verificar o número do balcão.',
            0,
            $exception
        );
    }
}
    public function criar(Balcao $balcao): bool
    {
        try {

            // SQL responsável por inserir um novo balcão
            $sql = "
                INSERT INTO balcoes (
                    numero,
                    nome,
                    tipo_atendimento_id
                )
                VALUES (
                    :numero,
                    :nome,
                    :tipo_atendimento_id
                )
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o INSERT
            $statement->execute([
                ':numero' => $balcao->getNumero(),
                ':nome' => $balcao->getNome(),
                ':tipo_atendimento_id' => $balcao->getTipoAtendimentoId()
            ]);

            return $this->sucesso($statement);

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao criar o balcão.',
                0,
                $exception
            );
        }
    }

    public function atualizar(
        int $id,
        Balcao $balcao
    ): bool {
        try {

            // SQL responsável por atualizar um balcão
            $sql = "
                UPDATE balcoes
                SET
                    numero = :numero,
                    nome = :nome,
                    tipo_atendimento_id = :tipo_atendimento_id
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o UPDATE
            $statement->execute([
                ':id' => $id,
                ':numero' => $balcao->getNumero(),
                ':nome' => $balcao->getNome(),
                ':tipo_atendimento_id' => $balcao->getTipoAtendimentoId()
            ]);

            return $this->sucesso($statement);

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao atualizar o balcão.',
                0,
                $exception
            );
        }
    }

    public function excluir(int $id): bool
    {
        try {

            // SQL responsável por excluir um balcão
            $sql = "
                DELETE FROM balcoes
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o DELETE
            $statement->execute([
                ':id' => $id
            ]);

            return $this->sucesso($statement);

        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao excluir o balcão.',
                0,
                $exception
            );
        }
    }
}