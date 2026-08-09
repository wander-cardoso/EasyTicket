<?php

namespace App\Services;

use App\Models\Senha;
use App\Repositories\SenhaRepository;
use InvalidArgumentException;

// Classe responsável pelas regras de negócio das Senhas
class SenhaService
{
    private SenhaRepository $repository;

    // Recebe o Repository por injeção de dependência
    public function __construct(SenhaRepository $repository)
    {
        $this->repository = $repository;
    }

    // Retorna todas as senhas
    /** @return Senha[] */
    public function listar(): array
    {
        return $this->repository->listar();
    }

    // Consulta uma senha pelo código
    public function consultarPorCodigo(string $codigo): ?Senha
    {
        if (trim($codigo) === '') {
            throw new InvalidArgumentException(
                'Informe o código da senha.'
            );
        }

        return $this->repository->consultarPorCodigo($codigo);
    }

    // Emite uma nova senha
    public function emitir(Senha $senha): array
    {
        try {

            if ($senha->getTipoAtendimentoId() <= 0) {
                throw new InvalidArgumentException(
                    'Selecione um tipo de atendimento.'
                );
            }

            if (
                !$this->repository->tipoAtendimentoExiste(
                    $senha->getTipoAtendimentoId()
                )
            ) {
                throw new InvalidArgumentException(
                    'O tipo de atendimento selecionado não está disponível.'
                );
            }

            // Inicia a transação
            $this->repository->iniciarTransacao();

            // Obtém o último código daquele tipo
            $ultimoCodigo = $this->repository->obterUltimoCodigo(
                $senha->getTipoAtendimentoId()
            );

            if ($ultimoCodigo !== null) {

                $sigla = $ultimoCodigo['sigla'];

                $numero = (int) substr(
                    $ultimoCodigo['codigo'],
                    strlen($sigla)
                );

                $proximoNumero = $numero + 1;

            } else {

                // Obtém a sigla do tipo de atendimento
                $sigla = $this->repository->obterSiglaTipoAtendimento(
                    $senha->getTipoAtendimentoId()
                );

                $proximoNumero = 1;
            }

            // Gera o código da senha
            $codigo = $sigla . str_pad(
                (string) $proximoNumero,
                3,
                '0',
                STR_PAD_LEFT
            );

            // Define a data e hora da emissão
            $dataEmissao = date('Y-m-d H:i:s');

            // Cria a senha com os dados controlados pelo backend
            $senha = new Senha(
                null,
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

            // Persiste a senha
            $this->repository->emitir($senha);

            // Confirma a transação
            $this->repository->confirmarTransacao();

            return [
                'codigo' => $codigo,
                'sigla' => $sigla,
                'dataEmissao' => $dataEmissao
            ];

        } catch (InvalidArgumentException $exception) {

            $this->repository->cancelarTransacao();

            throw $exception;

        } catch (\Throwable $exception) {

            $this->repository->cancelarTransacao();

            throw $exception;
        }
    }

    // Chama a próxima senha
    public function chamarProxima(int $balcaoId): Senha
    {
        if ($balcaoId <= 0) {
            throw new InvalidArgumentException(
                'Informe um balcão válido.'
            );
        }

        $senha = $this->repository->buscarProxima();

        if ($senha === null) {
            throw new InvalidArgumentException(
                'Não existem senhas aguardando atendimento.'
            );
        }

        $sucesso = $this->repository->chamarProxima(
            $senha->getId(),
            $balcaoId
        );

        if (!$sucesso) {
            throw new InvalidArgumentException(
                'Não foi possível chamar a próxima senha.'
            );
        }

        return $this->repository->consultarPorCodigo(
            $senha->getCodigo()
        );
    }

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento(int $id): Senha
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'Informe uma senha válida.'
            );
        }

        $senha = $this->obterSenhaPorId($id);

        if ($senha === null) {
            throw new InvalidArgumentException(
                'Senha não encontrada.'
            );
        }

        if ($senha->getStatus() !== 'chamada') {
            throw new InvalidArgumentException(
                'A senha precisa estar chamada para iniciar o atendimento.'
            );
        }

        $sucesso = $this->repository->iniciarAtendimento($id);

        if (!$sucesso) {
            throw new InvalidArgumentException(
                'Não foi possível iniciar o atendimento.'
            );
        }

        return $this->obterSenhaPorId($id);
    }

    // Finaliza o atendimento de uma senha
    public function finalizarAtendimento(int $id): Senha
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'Informe uma senha válida.'
            );
        }

        $senha = $this->obterSenhaPorId($id);

        if ($senha === null) {
            throw new InvalidArgumentException(
                'Senha não encontrada.'
            );
        }

        if ($senha->getStatus() !== 'em_atendimento') {
            throw new InvalidArgumentException(
                'A senha precisa estar em atendimento para ser finalizada.'
            );
        }

        $sucesso = $this->repository->finalizarAtendimento($id);

        if (!$sucesso) {
            throw new InvalidArgumentException(
                'Não foi possível finalizar o atendimento.'
            );
        }

        return $this->obterSenhaPorId($id);
    }

    // Obtém uma senha pelo ID
    private function obterSenhaPorId(int $id): ?Senha
    {
        return $this->repository->buscarPorId($id);
    }
}