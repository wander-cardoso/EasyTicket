<?php

namespace App\Interfaces\Repositories;

use App\Models\Utilizador;

interface UtilizadorRepositoryInterface
{
    // Busca um utilizador pelo nome de utilizador
    public function buscarPorNomeUtilizador(
        string $nomeUtilizador
    ): ?Utilizador;

    // Busca todos os utilizadores
    public function listar(): array;

    // Busca um utilizador pelo ID
    public function consultar(int $id): ?Utilizador;

    // Cria um novo utilizador
    public function criar(Utilizador $utilizador): Utilizador;

    // Edita um utilizador existente
    public function editar(Utilizador $utilizador): Utilizador;

    // Atualizar um utilizador Perfil (OPERADOR)
    public function atualizar(Utilizador $utilizador): Utilizador;

}