<?php

namespace App\Routes;

use App\Core\Router;
use App\Controllers\BalcaoController;
use App\Controllers\SenhaController;
use App\Controllers\TipoAtendimentoController;

// Registra todas as rotas da API.

function registerRoutes(Router $router): void
{
    // Tipos de Atendimento
    $router->get(
        '/api/tipos-atendimento',
        [TipoAtendimentoController::class, 'index']
    );

    $router->post(
        '/api/tipos-atendimento',
        [TipoAtendimentoController::class, 'store']
    );

    $router->put(
        '/api/tipos-atendimento/{id}',
        [TipoAtendimentoController::class, 'update']
    );

    $router->delete(
        '/api/tipos-atendimento/{id}',
        [TipoAtendimentoController::class, 'destroy']
    );

    // Balcões
    $router->get(
        '/api/balcoes',
        [BalcaoController::class, 'index']
    );

    $router->post(
        '/api/balcoes',
        [BalcaoController::class, 'store']
    );

    $router->put(
        '/api/balcoes/{id}',
        [BalcaoController::class, 'update'
        ]
    );

    $router->delete(
        '/api/balcoes/{id}',
        [BalcaoController::class, 'destroy']
    );

    // Senhas
    $router->get(
        '/api/senhas',
        [SenhaController::class, 'index']
    );

    $router->post(
        '/api/senhas',
        [SenhaController::class, 'store']
    );

    $router->post(
        '/api/senhas/chamar-proxima',
        [SenhaController::class, 'chamarProxima']
    );

    $router->post(
        '/api/senhas/iniciar-atendimento',
        [SenhaController::class, 'iniciarAtendimento']
    );

    $router->post(
        '/api/senhas/finalizar-atendimento',
        [SenhaController::class, 'finalizarAtendimento']
    );
}