<?php

namespace App\Interfaces\Repositories;

use App\Models\Balcao;

interface BalcaoRepositoryInterface
{
    // Retorna todos os balcões
    /** @return Balcao[] */
    public function listar(): array;

    // Persiste um novo balcão
    public function criar(Balcao $balcao): bool;

    // Atualiza um balcão existente
    public function atualizar(
        int $id,
        Balcao $balcao
    ): bool;

    // Exclui um balcão
    public function excluir(int $id): bool;

    // Verifica se o número do balcão já existe
    public function numeroExiste(
        int $numero,
        ?int $id = null
    ): bool;

    // Verifica se o nome do balcão já existe
    public function nomeExiste(
        string $nome,
        ?int $id = null
    ): bool;

}