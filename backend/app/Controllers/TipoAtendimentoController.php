<?php

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

    }

    public function criar(Request $request): void
    {

    }

    public function atualizar(
        Request $request,
        int $id
    ): void {

    }

    public function excluir(int $id): void
    {

    }
}