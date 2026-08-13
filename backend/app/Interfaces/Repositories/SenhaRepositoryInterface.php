<?php

namespace App\Interfaces\Repositories;

use App\Models\Senha;

interface SenhaRepositoryInterface
{
    // Retorna todas as senhas
    public function listar(): array;

    // Consulta uma senha pelo código
    public function consultarPorCodigo(
        string $codigo
    ): ?Senha;

    // Persiste uma nova senha
    public function emitirSenha(
        Senha $senha
    ): Senha;

    // Registra a chamada de uma senha
    public function chamarProxima(
        int $balcaoId,
        int $tipoAtendimentoId
    ): ?Senha;

    // Inicia o atendimento
    public function iniciarAtendimento(
        string $codigo,
        int $balcaoId
    ): bool;

    // Finaliza o atendimento
    public function finalizarAtendimento(
        string $codigo,
        int $balcaoId,
        ?string $nomeCliente,
        ?string $telefoneContacto
    ): bool;
}