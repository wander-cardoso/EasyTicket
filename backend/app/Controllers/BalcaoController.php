<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Balcao;
use App\Responses\JsonResponse;
use App\Services\BalcaoService;
use App\Exceptions\DatabaseException;
use InvalidArgumentException;

// Responsável por receber as requisições relacionadas aos Balcões
class BalcaoController
{
    private BalcaoService $service;

    // Recebe o Service por injeção de dependência
    public function __construct(BalcaoService $service)
    {
        $this->service = $service;
    }

    // Retorna todos os balcões
    public function listar(): void
    {
        try {

            $balcoes = $this->service->listar();

            JsonResponse::success(
                'Balcões listados com sucesso.',
                $balcoes
            );

        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao consultar os balcões.',
                500
            );
        }
    }

    // Cria um novo balcão
    public function criar(Request $request): void
    {
        try {

            $balcao = new Balcao(
                null,
                $request->input('numero'),
                $request->input('nome'),
                $request->input('tipoAtendimentoId')
            );

            $sucesso = $this->service->criar($balcao);

            if (!$sucesso) {
                JsonResponse::error(
                    'Não foi possível criar o balcão.',
                    500
                );

                return;
            }

            JsonResponse::success(
                'Balcão criado com sucesso.'
            );

        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );

        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao criar o balcão.',
                500
            );
        }
    }

    // Atualiza um balcão
    public function atualizar(
        Request $request,
        int $id
    ): void {
        try {

            $balcao = new Balcao(
                $id,
                $request->input('numero'),
                $request->input('nome'),
                $request->input('tipoAtendimentoId')
            );

            $sucesso = $this->service->atualizar(
                $id,
                $balcao
            );

            if (!$sucesso) {
                JsonResponse::error(
                    'Balcão não encontrado.',
                    404
                );

                return;
            }

            JsonResponse::success(
                'Balcão atualizado com sucesso.'
            );

        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );

        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao atualizar o balcão.',
                500
            );
        }
    }

    // Exclui um balcão
    public function excluir(int $id): void
    {
        try {

            $sucesso = $this->service->excluir($id);

            if (!$sucesso) {
                JsonResponse::error(
                    'Balcão não encontrado.',
                    404
                );

                return;
            }

            JsonResponse::success(
                'Balcão excluído com sucesso.'
            );

        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao excluir o balcão.',
                500
            );
        }
    }
}