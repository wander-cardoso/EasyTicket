<?php

namespace App\Controllers;

use App\Responses\JsonResponse;
use App\Services\AuthService;
use InvalidArgumentException;

// Responsável pelas requisições relacionadas à autenticação
class AuthController
{
    private AuthService $service;

    // Recebe o Service de autenticação
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    // Realiza o login do utilizador
    public function login(): void
    {
        try {
            // Obtém os dados enviados pela requisição
            $dados = json_decode(
                file_get_contents('php://input'),
                true
            );

            // Realiza a autenticação
            $resultado = $this->service->autenticar(
                $dados['nomeUtilizador'] ?? '',
                $dados['password'] ?? ''
            );

            // Retorna o token e os dados públicos do utilizador
            JsonResponse::success(
                'Login realizado com sucesso.',
                $resultado
            );

        } catch (InvalidArgumentException $exception) {
            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        }
    }
}