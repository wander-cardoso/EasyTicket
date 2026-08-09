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
    public function listar(int $pagina = 1, int $limite = 10): array
    {
        try {
            return $this->repository->listar($pagina, $limite);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    // Consulta uma senha pelo código
    public function consultarPorCodigo(string $codigo): ?Senha
    {
        try {
            if (trim($codigo) === '') {
                throw new InvalidArgumentException('Informe o código da senha.');
            }
            return $this->repository->consultarPorCodigo($codigo);
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    // Emite uma nova senha
    public function emitirSenha(Senha $senha): Senha
    {
        try {
            // Valida o tipo de atendimento
            if ($senha->getTipoAtendimentoId() <= 0) {
                throw new InvalidArgumentException('Selecione um tipo de atendimento.');
            }
            // Emite a senha
            return $this->repository->emitirSenha($senha);
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    // Chama a próxima senha
    public function chamarProxima(int $balcaoId): ?Senha
    {
        try {
            // Valida o balcão
            if ($balcaoId <= 0) {
                throw new InvalidArgumentException('Informe um balcão válido.');
            }

            $senha = $this->repository->chamarProxima($balcaoId);

            // Nenhuma senha aguardando atendimento
            if ($senha === null) {
                throw new InvalidArgumentException(
                    'Não existem senhas aguardando atendimento.'
                );
            }
            // Retorna a senha que foi chamada
            return $senha;
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento(string $codigo): bool
    {
        try {
            // Valida o código informado
            if (trim($codigo) === '') {
                throw new InvalidArgumentException('Informe o código da senha.');
            }
            // Busca a senha pelo código
            $senha = $this->repository->consultarPorCodigo($codigo);

            // Verifica se a senha existe
            if ($senha === null) {
                throw new InvalidArgumentException('Senha não encontrada.');
            }

            // Verifica se a senha foi chamada
            if ($senha->getStatus() !== 'chamada') {
                throw new InvalidArgumentException(
                    'A senha precisa estar no estado "chamada" para iniciar o atendimento.'
                );
            }
            // Inicia o atendimento
            return $this->repository->iniciarAtendimento($codigo);
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    // Finaliza o atendimento de uma senha
    public function finalizarAtendimento(string $codigo): bool
    {
        try {
            // Valida o código informado
            if (trim($codigo) === '') {
                throw new InvalidArgumentException(
                    'Informe o código da senha.'
                );
            }
            // Busca a senha pelo código
            $senha = $this->repository->consultarPorCodigo($codigo);

            // Verifica se a senha existe
            if ($senha === null) {
                throw new InvalidArgumentException('Senha não encontrada.');
            }

            // Verifica se a senha está em atendimento
            if ($senha->getStatus() !== 'em_atendimento') {
                throw new InvalidArgumentException(
                    'A senha precisa estar em atendimento para ser finalizada.'
                );
            }

            // Finaliza o atendimento
            return $this->repository->finalizarAtendimento($codigo);
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
