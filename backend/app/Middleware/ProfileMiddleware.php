<?php

namespace App\Middleware;

use App\Responses\JsonResponse;

// Responsável por verificar se o utilizador possui o perfil necessário
class ProfileMiddleware
{
    // Verifica se o perfil do utilizador possui autorização
    public function verificar(
        array $utilizador,
        array $perfisPermitidos
    ): void {
        // Obtém o perfil presente no payload do JWT
        $perfil = $utilizador['perfil'] ?? null;

        // Verifica se o utilizador possui um perfil permitido
        if (
            $perfil === null ||
            !in_array($perfil, $perfisPermitidos, true)
        ) {
            JsonResponse::error(
                'Não possui autorização para executar esta operação.',
                403
            );
        }
    }
}