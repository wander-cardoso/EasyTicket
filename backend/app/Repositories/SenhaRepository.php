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
    public function listar(
        int $pagina = 1,
        int $limite = 10
    ): array {
        try {

            // Garante que a página nunca seja menor que 1
            $pagina = max(1, $pagina);

            // Garante que o limite seja positivo
            $limite = max(1, $limite);

            // Calcula quantos registros devem ser ignorados
            $offset = ($pagina - 1) * $limite;

            // SQL responsável por buscar as senhas
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
                LIMIT :limite OFFSET :offset
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT substituindo as variaveis da stringSql pelos valores das variaveis
            $statement->execute([
                ':limite' => $limite,
                ':offset' => $offset
            ]);

            // Obtém os registos
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

// Emite uma nova senha
public function emitirSenha(Senha $senha): Senha
{
    try {

        // Inicia a transação
        $this->connection->beginTransaction();

        // Busca a sigla do tipo de atendimento
        $sql = "
            SELECT
                sigla
            FROM tipos_atendimento
            WHERE id = :tipo_atendimento_id
            FOR UPDATE
        ";

        // Prepara a query
        $statement = $this->connection->prepare($sql);

        // Executa o SELECT
        $statement->execute([
            ':tipo_atendimento_id' => $senha->getTipoAtendimentoId()
        ]);

        // Obtém o tipo de atendimento
        $tipoAtendimento = $statement->fetch(PDO::FETCH_ASSOC);

        // Tipo de atendimento não encontrado
        if (!$tipoAtendimento) {

            throw new DatabaseException(
                'Tipo de atendimento não encontrado.'
            );
        }

        // Obtém a sigla
        $sigla = $tipoAtendimento['sigla'];

        // Busca o último código emitido para esse tipo
        $sql = "
            SELECT codigo
            FROM senhas
            WHERE tipo_atendimento_id = :tipo_atendimento_id
            ORDER BY id DESC
            LIMIT 1
        ";

        // Prepara a query
        $statement = $this->connection->prepare($sql);

        // Executa o SELECT
        $statement->execute([
            ':tipo_atendimento_id' => $senha->getTipoAtendimentoId()
        ]);

        // Obtém o último código
        $ultimoCodigo = $statement->fetchColumn();

        // Define o próximo número
        if ($ultimoCodigo === false) {

            $proximoNumero = 1;

        } else {

            // Remove a sigla e mantém apenas o número
            $numero = (int) substr(
                $ultimoCodigo,
                strlen($sigla)
            );

            $proximoNumero = $numero + 1;
        }

        // Gera o código
        $codigo = $sigla . str_pad(
            (string) $proximoNumero,
            3,
            '0',
            STR_PAD_LEFT
        );

        // Define a data e hora da emissão
        $dataEmissao = date('Y-m-d H:i:s');

        // Insere a senha
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
                NULL,
                'emitida',
                :data_emissao
            )
        ";

        // Prepara o INSERT
        $statement = $this->connection->prepare($sql);

        // Executa o INSERT
        $statement->execute([
            ':codigo' => $codigo,
            ':nome_cliente' => $senha->getNomeCliente(),
            ':telefone_contacto' => $senha->getTelefoneContacto(),
            ':tipo_atendimento_id' => $senha->getTipoAtendimentoId(),
            ':data_emissao' => $dataEmissao
        ]);

        // Obtém o ID gerado pelo banco
        $id = (int) $this->connection->lastInsertId();

        // Cria a senha completa que foi emitida
        $senhaEmitida = new Senha(
            $id,
            $codigo,
            $senha->getNomeCliente(),
            $senha->getTelefoneContacto(),
            $senha->getTipoAtendimentoId(),
            null,
            'emitida',
            $dataEmissao,
            null,
            null,
            null
        );

        // Confirma a transação
        $this->connection->commit();

        // Retorna a senha emitida
        return $senhaEmitida;

    } catch (PDOException $exception) {

        // Desfaz a transação caso tenha ocorrido algum erro
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }

        throw new DatabaseException(
            'Erro ao emitir a senha.',
            0,
            $exception
        );

    } catch (DatabaseException $exception) {

        // Desfaz a transação caso tenha ocorrido algum erro
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }

        throw $exception;
    }
}

    // Registra a chamada de uma senha
    public function chamarProxima( int $balcaoId ): ?Senha {
        try {

            // Inicia a transação
            $this->connection->beginTransaction();

            // SQL responsável por buscar e bloquear a próxima senha
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
                FOR UPDATE
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o SELECT
            $statement->execute();

            // Obtém a próxima senha
            $registo = $statement->fetch(PDO::FETCH_ASSOC);

            // Não existe senha aguardando
            if (!$registo) {

                $this->connection->commit();

                return null;
            }

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
                ':id' => $registo['id'],
                ':balcao_id' => $balcaoId
            ]);

            // Confirma a transação
            $this->connection->commit();

            return new Senha(
                $registo['id'],
                $registo['codigo'],
                $registo['nome_cliente'],
                $registo['telefone_contacto'],
                $registo['tipo_atendimento_id'],
                $balcaoId,
                'chamada',
                $registo['data_emissao'],
                date('Y-m-d H:i:s'),
                $registo['data_inicio_atendimento'],
                $registo['data_finalizacao']
            );

        } catch (PDOException $exception) {

            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw new DatabaseException(
                'Erro ao chamar a senha.',
                0,
                $exception
            );
        }
    }

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento( string $codigo ): bool {
        try {

            // SQL responsável por iniciar o atendimento
            $sql = "
                UPDATE senhas
                SET
                    status = 'em_atendimento',
                    data_inicio_atendimento = NOW()
                WHERE codigo = :codigo
                AND status = 'chamada'
            ";

            // Prepara a query
            $statement = $this->connection->prepare($sql);

            // Executa o UPDATE
            $statement->execute([
                ':codigo' => $codigo
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
public function finalizarAtendimento( string $codigo ): bool {
    try {

        // SQL responsável por finalizar o atendimento
        $sql = "
            UPDATE senhas
            SET
                status = 'finalizada',
                data_finalizacao = NOW()
            WHERE codigo = :codigo
            AND status = 'em_atendimento'
        ";

        // Prepara a query
        $statement = $this->connection->prepare($sql);

        // Executa o UPDATE
        $statement->execute([
            ':codigo' => $codigo
        ]);

        // Verifica se alguma senha foi atualizada
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