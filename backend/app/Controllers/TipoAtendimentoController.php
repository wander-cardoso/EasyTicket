<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\TipoAtendimento;
use App\Responses\JsonResponse;

namespace App\Controllers;

use App\Core\Request;
use App\Models\TipoAtendimento;
use App\Responses\JsonResponse;
use App\Services\TipoAtendimentoService;

// Responsável por receber as requisições relacionadas aos Tipos de Atendimento 
class TipoAtendimentoController
{
    private TipoAtendimentoService $service;

    // Recebe o Service por injeção de dependência
    public function __construct(TipoAtendimentoService $service)
    {
        $this->service = $service;
    }

    public function listar(): void
    {
        $tiposAtendimento = $this->service->listar();

        JsonResponse::success(
            'Tipos de atendimento listados com sucesso.',
            $tiposAtendimento
        );
    }

    public function criar(Request $request): void
    {
        $tipoAtendimento = new TipoAtendimento(
            null,
            $request->input('nome'),
            $request->input('sigla'),
            $request->input('descricao')
        );

        $sucesso = $this->service->criar($tipoAtendimento);

        if (!$sucesso) {
            JsonResponse::error(
                'Não foi possível criar o tipo de atendimento.'
            );

            return;
        }

        JsonResponse::success(
            'Tipo de atendimento criado com sucesso.'
        );
    }

    public function atualizar(
        Request $request,
        int $id
    ): void {

        $tipoAtendimento = new TipoAtendimento(
            $id,
            $request->input('nome'),
            $request->input('sigla'),
            $request->input('descricao')
        );

        $sucesso = $this->service->atualizar(
            $id,
            $tipoAtendimento
        );

        if (!$sucesso) {
            JsonResponse::error(
                'Tipo de atendimento não encontrado.',
                404
            );

            return;
        }

        JsonResponse::success(
            'Tipo de atendimento atualizado com sucesso.'
        );
    }

    public function excluir(int $id): void
    {
        $sucesso = $this->service->excluir($id);

        if (!$sucesso) {
            JsonResponse::error(
                'Tipo de atendimento não encontrado.',
                404
            );

            return;
        }

        JsonResponse::success(
            'Tipo de atendimento excluído com sucesso.'
        );
    }
}