<?php

namespace App\Repositories;

use PDO;
use PDOException;
use App\Models\Senha;
use App\Exceptions\DatabaseException;
use App\Interfaces\Repositories\SenhaRepositoryInterface;

// Classe que manipula banco de dados na tabela senhas
class SenhaRepository extends BaseRepository implements SenhaRepositoryInterface
{
    // Retorna todas as senhas
    public function listar(): array
    {
        try {

            // SQL responsável por buscar todas as senhas
            $sql = "
                SELECT
                    id,
                    codigo,
                    nome_cliente,
                    telefone_contacto,
                    tipo_atendimento_id,
                    balcao_id,
                    status,
                    data_emissao,
                    data_chamada,
                    data_inicio_atendimento,
                    data_finalizacao
                FROM senhas
                ORDER BY data_emissao DESC
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute();

            // Obtém todos os registos
            $registos = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Lista que será devolvida ao Service
            $senhas = [];

            // Converte cada registo em um objeto
            foreach ($registos as $registo) {

                $senhas[] = new Senha(
                    $registo['id'],
                    $registo['codigo'],
                    $registo['nome_cliente'],
                    $registo['telefone_contacto'],
                    $registo['tipo_atendimento_id'],
                    $registo['balcao_id'],
                    $registo['status'],
                    $registo['data_emissao'],
                    $registo['data_chamada'],
                    $registo['data_inicio_atendimento'],
                    $registo['data_finalizacao']
                );
            }

            return $senhas;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao listar as senhas.',
                0,
                $exception
            );
        }
    }

    // Consulta uma senha pelo código
    public function consultarPorCodigo(string $codigo): ?Senha
    {
        try {

            // SQL responsável por consultar uma senha específica
            $sql = "
                SELECT
                    id,
                    codigo,
                    nome_cliente,
                    telefone_contacto,
                    tipo_atendimento_id,
                    balcao_id,
                    status,
                    data_emissao,
                    data_chamada,
                    data_inicio_atendimento,
                    data_finalizacao
                FROM senhas
                WHERE codigo = :codigo
                LIMIT 1
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute([
                ':codigo' => $codigo
            ]);

            // Obtém o registo
            $registo = $statement->fetch(PDO::FETCH_ASSOC);

            // Retorna null caso a senha não exista
            if (!$registo) {
                return null;
            }

            return new Senha(
                $registo['id'],
                $registo['codigo'],
                $registo['nome_cliente'],
                $registo['telefone_contacto'],
                $registo['tipo_atendimento_id'],
                $registo['balcao_id'],
                $registo['status'],
                $registo['data_emissao'],
                $registo['data_chamada'],
                $registo['data_inicio_atendimento'],
                $registo['data_finalizacao']
            );
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao consultar a senha.',
                0,
                $exception
            );
        }
    }
    // Busca uma senha pelo ID
    public function buscarPorId(int $id): ?Senha
    {
        try {

            // SQL responsável por buscar uma senha pelo ID
            $sql = "
            SELECT
                id,
                codigo,
                nome_cliente,
                telefone_contacto,
                tipo_atendimento_id,
                balcao_id,
                status,
                data_emissao,
                data_chamada,
                data_inicio_atendimento,
                data_finalizacao
            FROM senhas
            WHERE id = :id
            LIMIT 1
        ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute([
                ':id' => $id
            ]);

            // Obtém o registo
            $registo = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$registo) {
                return null;
            }

            return new Senha(
                $registo['id'],
                $registo['codigo'],
                $registo['nome_cliente'],
                $registo['telefone_contacto'],
                $registo['tipo_atendimento_id'],
                $registo['balcao_id'],
                $registo['status'],
                $registo['data_emissao'],
                $registo['data_chamada'],
                $registo['data_inicio_atendimento'],
                $registo['data_finalizacao']
            );
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao buscar a senha.',
                0,
                $exception
            );
        }
    }
    // Inicia uma transação
    public function iniciarTransacao(): void
    {
        $this->connection->beginTransaction();
    }

    // Confirma uma transação
    public function confirmarTransacao(): void
    {
        $this->connection->commit();
    }

    // Cancela uma transação
    public function cancelarTransacao(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
    }

    // Obtém o último código emitido para um tipo de atendimento
    public function obterUltimoCodigo(
        int $tipoAtendimentoId
    ): ?array {
        try {

            // SQL responsável por buscar o último código
            // e a sigla do tipo de atendimento
            $sql = "
            SELECT
                s.codigo,
                ta.sigla
            FROM senhas s
            INNER JOIN tipos_atendimento ta
                ON ta.id = s.tipo_atendimento_id
            WHERE s.tipo_atendimento_id = :tipo_atendimento_id
            ORDER BY s.id DESC
            LIMIT 1
            FOR UPDATE
        ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute([
                ':tipo_atendimento_id' => $tipoAtendimentoId
            ]);

            // Obtém o último registo
            $registo = $statement->fetch(PDO::FETCH_ASSOC);

            // Não existe senha anterior
            if (!$registo) {
                return null;
            }

            return [
                'codigo' => $registo['codigo'],
                'sigla' => $registo['sigla']
            ];
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao obter o último código da senha.',
                0,
                $exception
            );
        }
    }

    // Emite uma nova senha
    // Emite uma nova senha
    public function emitir(Senha $senha): bool
    {
        try {

            // SQL responsável por inserir uma nova senha
            $sql = "
            INSERT INTO senhas (
                codigo,
                nome_cliente,
                telefone_contacto,
                tipo_atendimento_id,
                balcao_id,
                status,
                data_emissao
            )
            VALUES (
                :codigo,
                :nome_cliente,
                :telefone_contacto,
                :tipo_atendimento_id,
                :balcao_id,
                :status,
                :data_emissao
            )
        ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o INSERT
            $statement->execute([
                ':codigo' => $senha->getCodigo(),
                ':nome_cliente' => $senha->getNomeCliente(),
                ':telefone_contacto' => $senha->getTelefoneContacto(),
                ':tipo_atendimento_id' => $senha->getTipoAtendimentoId(),
                ':balcao_id' => $senha->getBalcaoId(),
                ':status' => $senha->getStatus(),
                ':data_emissao' => $senha->getDataEmissao()
            ]);

            return true;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao emitir a senha.',
                0,
                $exception
            );
        }
    }

    // Busca a próxima senha que deve ser chamada
    public function buscarProxima(): ?Senha
    {
        try {

            // SQL responsável por buscar a próxima senha
            $sql = "
                SELECT
                    id,
                    codigo,
                    nome_cliente,
                    telefone_contacto,
                    tipo_atendimento_id,
                    balcao_id,
                    status,
                    data_emissao,
                    data_chamada,
                    data_inicio_atendimento,
                    data_finalizacao
                FROM senhas
                WHERE status = 'emitida'
                ORDER BY data_emissao ASC
                LIMIT 1
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute();

            // Obtém a próxima senha
            $registo = $statement->fetch(PDO::FETCH_ASSOC);

            // Não existe senha aguardando
            if (!$registo) {
                return null;
            }

            return new Senha(
                $registo['id'],
                $registo['codigo'],
                $registo['nome_cliente'],
                $registo['telefone_contacto'],
                $registo['tipo_atendimento_id'],
                $registo['balcao_id'],
                $registo['status'],
                $registo['data_emissao'],
                $registo['data_chamada'],
                $registo['data_inicio_atendimento'],
                $registo['data_finalizacao']
            );
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao buscar a próxima senha.',
                0,
                $exception
            );
        }
    }
    // Verifica se o tipo de atendimento existe
    public function tipoAtendimentoExiste(
        int $tipoAtendimentoId
    ): bool {
        try {

            // SQL responsável por verificar se o tipo de atendimento existe
            $sql = "
            SELECT COUNT(*)
            FROM tipos_atendimento
            WHERE id = :id
        ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute([
                ':id' => $tipoAtendimentoId
            ]);

            // Obtém a quantidade de registros encontrados
            $quantidade = (int) $statement->fetchColumn();

            return $quantidade > 0;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao verificar o tipo de atendimento.',
                0,
                $exception
            );
        }
    }

    // Obtém a sigla do tipo de atendimento
    public function obterSiglaTipoAtendimento(
        int $tipoAtendimentoId
    ): string {
        try {

            // SQL responsável por buscar a sigla do tipo de atendimento
            $sql = "
            SELECT sigla
            FROM tipos_atendimento
            WHERE id = :id
        ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute([
                ':id' => $tipoAtendimentoId
            ]);

            // Obtém a sigla
            $sigla = $statement->fetchColumn();

            if ($sigla === false) {
                throw new DatabaseException(
                    'Tipo de atendimento não encontrado.'
                );
            }

            return $sigla;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao obter a sigla do tipo de atendimento.',
                0,
                $exception
            );
        }
    }
    // Registra a chamada de uma senha
    public function chamarProxima(
        int $id,
        int $balcaoId
    ): bool {
        try {

            // SQL responsável por registrar a chamada
            $sql = "
                UPDATE senhas
                SET
                    status = 'chamada',
                    balcao_id = :balcao_id,
                    data_chamada = NOW()
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o UPDATE
            $statement->execute([
                ':id' => $id,
                ':balcao_id' => $balcaoId
            ]);

            return $statement->rowCount() > 0;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao chamar a senha.',
                0,
                $exception
            );
        }
    }

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento(int $id): bool
    {
        try {

            // SQL responsável por iniciar o atendimento
            $sql = "
                UPDATE senhas
                SET
                    status = 'em_atendimento',
                    data_inicio_atendimento = NOW()
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o UPDATE
            $statement->execute([
                ':id' => $id
            ]);

            return $statement->rowCount() > 0;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao iniciar o atendimento.',
                0,
                $exception
            );
        }
    }

    // Finaliza o atendimento de uma senha
    public function finalizarAtendimento(int $id): bool
    {
        try {

            // SQL responsável por finalizar o atendimento
            $sql = "
                UPDATE senhas
                SET
                    status = 'finalizada',
                    data_finalizacao = NOW()
                WHERE id = :id
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o UPDATE
            $statement->execute([
                ':id' => $id
            ]);

            return $statement->rowCount() > 0;
        } catch (PDOException $exception) {

            throw new DatabaseException(
                'Erro ao finalizar o atendimento.',
                0,
                $exception
            );
        }
    }
}
