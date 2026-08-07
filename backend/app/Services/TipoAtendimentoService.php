<?php

namespace App\Services;

use App\Models\TipoAtendimento;
use App\Repositories\TipoAtendimentoRepository;

// Classe responsável pelas regras de negócio dos Tipos de Atendimento
class TipoAtendimentoService
{
    private TipoAtendimentoRepository $repository;

    // Recebe o Repository por injeção de dependência
    public function __construct(TipoAtendimentoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Retorna todos os tipos de atendimento
    /** @return TipoAtendimento[] */
    public function listar(): array
    {
        return $this->repository->listar();
    }

    // Cria um novo tipo de atendimento
    public function criar(TipoAtendimento $tipoAtendimento): bool
    {
        return $this->repository->criar($tipoAtendimento);
    }

    // Atualiza um tipo de atendimento
    public function atualizar(
        int $id,
        TipoAtendimento $tipoAtendimento
    ): bool {
        return $this->repository->atualizar(
            $id,
            $tipoAtendimento
        );
    }

    // Exclui um tipo de atendimento
    public function excluir(int $id): bool
    {
        return $this->repository->excluir($id);
    }
}