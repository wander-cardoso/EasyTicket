<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Responses\JsonResponse;

// Responsável por validar o JWT das rotas protegidas
class AuthMiddleware
{
    // Valida o token JWT enviado na requisição
    public function verificar(): array
    {
        // Obtém o cabeçalho Authorization
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Verifica se o cabeçalho possui o formato Bearer
        if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            JsonResponse::error(
                'Token de autenticação não informado.',
                401
            );
        }

        // Obtém somente o token
        $token = $matches[1];

        // Obtém a chave secreta usada para assinar o JWT
        $chave = $_ENV['JWT_SECRET'] ?? '';

        if ($chave === '') {
            JsonResponse::error(
                'JWT_SECRET não configurado.',
                500
            );
        }
        try {
            // Decodifica e valida o JWT
            $dados = JWT::decode(
                $token,
                new Key($chave, 'HS256')
            );

            // Converte o payload para array
            return (array) $dados;
        } catch (\Throwable $exception) {

            // Registra o erro internamente
            error_log(
                'Erro na validação do JWT: ' . $exception->getMessage()
            );

            // Não expõe detalhes internos ao cliente
            JsonResponse::error(
                'Token de autenticação inválido ou expirado.',
                401
            );
        }

        return [];
    }
}
