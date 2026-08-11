<?php

namespace App\Controllers;

use App\Services\UtilizadorService;
use App\Responses\JsonResponse;
use InvalidArgumentException;

// Responsável pelas requisições relacionadas aos utilizadores
class UtilizadorController
{
    private UtilizadorService $service;

    // Recebe o Service por injeção de dependência
    public function __construct(UtilizadorService $service)
    {
        $this->service = $service;
    }

    // Cria um novo utilizador
    public function criar(): void
    {
        try {
            // Obtém os dados enviados pela requisição
            $dados = json_decode(
                file_get_contents('php://input'),
                true
            );

            // Cria o utilizador através do Service
            $utilizador = $this->service->criar(
                $dados['nome'] ?? '',
                $dados['nomeUtilizador'] ?? '',
                $dados['password'] ?? '',
                $dados['perfil'] ?? ''
            );

            // Retorna os dados do utilizador criado
            // Retorna os dados do utilizador criado
            JsonResponse::success(
                'Utilizador criado com sucesso.',
                [
                    'id' => $utilizador->getId(),
                    'nome' => $utilizador->getNome(),
                    'nomeUtilizador' => $utilizador->getNomeUtilizador(),
                    'perfil' => $utilizador->getPerfil(),
                    'criadoEm' => $utilizador->getCriadoEm()
                ],
                201
            );
        } catch (InvalidArgumentException $exception) {
            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        }
    }

    // Lista todos os utilizadores
public function listar(): void
{
    try {
        $utilizadores = $this->service->listar();

        $dados = [];

        foreach ($utilizadores as $utilizador) {
            $dados[] = [
                'id' => $utilizador->getId(),
                'nome' => $utilizador->getNome(),
                'nomeUtilizador' => $utilizador->getNomeUtilizador(),
                'perfil' => $utilizador->getPerfil(),
                'criadoEm' => $utilizador->getCriadoEm()
            ];
        }

        JsonResponse::success(
            'Utilizadores listados com sucesso.',
            $dados
        );

    } catch (\Throwable $exception) {
        JsonResponse::error(
            'Erro ao listar os utilizadores.',
            500
        );
    }
}

// Consulta um utilizador pelo ID
public function consultar(string $id): void
{
    try {
        $utilizador = $this->service->consultar(
            (int) $id
        );

        JsonResponse::success(
            'Utilizador encontrado.',
            [
                'id' => $utilizador->getId(),
                'nome' => $utilizador->getNome(),
                'nomeUtilizador' => $utilizador->getNomeUtilizador(),
                'perfil' => $utilizador->getPerfil(),
                'criadoEm' => $utilizador->getCriadoEm()
            ]
        );

    } catch (InvalidArgumentException $exception) {
        JsonResponse::error(
            $exception->getMessage(),
            404
        );
    }
}

// Edita um utilizador
public function editar(string $id): void
{
    try {
        $dados = json_decode(
            file_get_contents('php://input'),
            true
        );

        $utilizador = $this->service->editar(
            (int) $id,
            $dados['nome'] ?? '',
            $dados['nomeUtilizador'] ?? '',
            $dados['perfil'] ?? '',
            $dados['password'] ?? null
        );

        JsonResponse::success(
            'Utilizador atualizado com sucesso.',
            [
                'id' => $utilizador->getId(),
                'nome' => $utilizador->getNome(),
                'nomeUtilizador' => $utilizador->getNomeUtilizador(),
                'perfil' => $utilizador->getPerfil(),
                'criadoEm' => $utilizador->getCriadoEm()
            ]
        );

    } catch (InvalidArgumentException $exception) {
        JsonResponse::error(
            $exception->getMessage(),
            400
        );
    }
}
}
