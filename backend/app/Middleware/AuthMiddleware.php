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
        // Obtém o cabeçalho Authorization da requisição
        $authorization = $this->obterAuthorization();

        // Verifica se o cabeçalho possui o formato Bearer
        if (
            !preg_match(
                '/^Bearer\s+(.+)$/i',
                $authorization,
                $matches
            )
        ) {
            // Retorna erro quando o token não foi enviado
            JsonResponse::error(
                'Token de autenticação não informado.',
                401
            );

            return [];
        }

        // Obtém somente o JWT, removendo o prefixo "Bearer"
        $token = $matches[1];

        // Obtém a chave secreta utilizada para assinar o JWT
        $chave = $_ENV['JWT_SECRET'] ?? '';

        // Verifica se a chave secreta está configurada
        if ($chave === '') {
            // Retorna erro de configuração interna
            JsonResponse::error(
                'JWT_SECRET não configurado.',
                500
            );

            return [];
        }

        try {
            // Decodifica e valida o JWT utilizando a chave secreta
            $dados = JWT::decode(
                $token,
                new Key($chave, 'HS256')
            );

            // Converte o payload do JWT para um array
            return (array) $dados;

        } catch (\Throwable $exception) {

            // Registra internamente o motivo da falha
            // sem expor detalhes do JWT ao utilizador
            error_log(
                'Erro na validação do JWT: ' .
                $exception->getMessage()
            );

            // Retorna uma mensagem genérica ao cliente
            JsonResponse::error(
                'Token de autenticação inválido ou expirado.',
                401
            );

            return [];
        }
    }


    // Obtém o cabeçalho Authorization da requisição
    private function obterAuthorization(): string
    {
        // Tenta obter o Authorization através do servidor PHP
        $authorization =
            $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Retorna o cabeçalho quando estiver disponível
        if ($authorization !== '') {
            return $authorization;
        }


        // Tenta obter os cabeçalhos através do getallheaders()
        if (function_exists('getallheaders')) {

            // Obtém todos os cabeçalhos recebidos pelo PHP
            $headers = getallheaders();

            // Percorre os cabeçalhos recebidos
            foreach ($headers as $nome => $valor) {

                // Procura Authorization ignorando maiúsculas/minúsculas
                if (
                    strcasecmp(
                        $nome,
                        'Authorization'
                    ) === 0
                ) {
                    // Retorna o valor do Authorization
                    return $valor;
                }
            }
        }


        // Tenta obter o Authorization através de outra variável
        // utilizada por alguns ambientes de servidor
        $authorization =
            $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        // Retorna o cabeçalho quando estiver disponível
        if ($authorization !== '') {
            return $authorization;
        }


        // Nenhuma fonte disponibilizou o Authorization
        return '';
    }
}