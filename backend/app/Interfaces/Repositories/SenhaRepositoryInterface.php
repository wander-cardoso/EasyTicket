<?php

namespace App\Interfaces\Repositories;

use App\Models\Senha;

interface SenhaRepositoryInterface
{
    // Retorna todas as senhas
    public function listar(): array;

    // Consulta uma senha pelo código
    public function consultarPorCodigo(string $codigo): ?Senha;

    // Persiste uma nova senha
    public function emitirSenha(Senha $senha): Senha;

    // Registra a chamada de uma senha
    public function chamarProxima( int $balcaoId ): ?Senha;

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento(string $codigo): bool;

    // Finaliza o atendimento de uma senha
    public function finalizarAtendimento(string $codigo): bool;
}
