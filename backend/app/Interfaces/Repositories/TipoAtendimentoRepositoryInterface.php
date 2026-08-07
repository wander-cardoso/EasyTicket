<?php

namespace App\Interfaces\Repositories;

use App\Models\TipoAtendimento;

interface TipoAtendimentoRepositoryInterface
{
    // Retorna todos os tipos de atendimento 
    /** @return TipoAtendimento[] */
    public function listar(): array;

    // Persiste um novo tipo de atendimento
    public function criar(TipoAtendimento $tipoAtendimento): bool;

    // Atualiza um tipo de atendimento existente
    public function atualizar(
        int $id,
        TipoAtendimento $tipoAtendimento
    ): bool;

    // Exclui um tipo de atendimento
    public function excluir(int $id): bool;
}