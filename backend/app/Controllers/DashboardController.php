<?php

namespace App\Controllers;

use App\Core\Request;
use App\Responses\JsonResponse;
use App\Services\DashboardService;
use App\Exceptions\DatabaseException;
use InvalidArgumentException;

// Responsável pelas requisições relacionadas ao Dashboard
class DashboardController
{
    private DashboardService $service;


    // Recebe o Service por injeção de dependência
    public function __construct(
        DashboardService $service
    ) {
        $this->service = $service;
    }


    // Retorna os dados do Dashboard do utilizador autenticado
    public function obter(Request $request): void
    {
        try {

            // Obtém os dados do utilizador validados pelo AuthMiddleware
            $dadosAutenticacao =
                $request->utilizadorAutenticado();


            // Obtém o ID do utilizador através do claim "sub" do JWT
            $utilizadorId = (int) (
                $dadosAutenticacao['sub'] ?? 0
            );


            // Verifica se o ID do utilizador é válido
            if ($utilizadorId <= 0) {

                JsonResponse::error(
                    'Utilizador autenticado inválido.',
                    401
                );

                return;
            }


            // Obtém o ID do balcão armazenado no JWT
            $balcaoId = isset(
                $dadosAutenticacao['balcaoId']
            )
                ? (int) $dadosAutenticacao['balcaoId']
                : null;


            // Obtém os dados necessários para o Dashboard
            $dados = $this->service->obter(
                $utilizadorId,
                $balcaoId
            );


            // Retorna os dados para o Angular
            JsonResponse::success(
                'Dashboard carregado com sucesso.',
                $dados
            );

        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                404
            );

        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao carregar o Dashboard.',
                500
            );
        }
    }
}