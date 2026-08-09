<?php

namespace App\Interfaces\Repositories;

use App\Models\Senha;

interface SenhaRepositoryInterface
{
    // Retorna todas as senhas
    /** @return Senha[] */
    public function listar(): array;

    // Consulta uma senha pelo código
    public function consultarPorCodigo(string $codigo): ?Senha;

    //Consulta a Ultima Senha gerada antes de emitir uma nova
    public function obterUltimoCodigo(
        int $tipoAtendimentoId
    ): ?array;

    // Inicia uma transação
    public function iniciarTransacao(): void;

    // Confirma uma transação
    public function confirmarTransacao(): void;

    // Cancela uma transação
    public function cancelarTransacao(): void;

    // Busca a próxima senha que deve ser chamada
    public function buscarProxima(): ?Senha;

    // Busca uma senha pelo ID
    public function buscarPorId(int $id): ?Senha;

    // Persiste uma nova senha
    public function emitir(Senha $senha): bool;

    // Verifica se o tipo de atendimento existe
    public function tipoAtendimentoExiste(
        int $tipoAtendimentoId
    ): bool;
    // Obtém a sigla do tipo de atendimento
    public function obterSiglaTipoAtendimento(
        int $tipoAtendimentoId
    ): string;
    // Registra a chamada de uma senha
    public function chamarProxima(
        int $id,
        int $balcaoId
    ): bool;

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento(int $id): bool;

    // Finaliza o atendimento de uma senha
    public function finalizarAtendimento(int $id): bool;
}
