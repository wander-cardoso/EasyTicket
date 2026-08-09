<?php

namespace App\Routes;

use App\Core\Router;
use App\Controllers\BalcaoController;
use App\Controllers\SenhaController;
use App\Controllers\TipoAtendimentoController;

// Registra todas as rotas da API.

function registerRoutes(
    Router $router,
    TipoAtendimentoController $tipoAtendimentoController,
    BalcaoController $balcaoController,
    SenhaController $senhaController

): void {
    // Tipos de Atendimento
    $router->get(
        '/api/tipos-atendimento',
        [$tipoAtendimentoController, 'listar']
    );

    $router->post(
        '/api/tipos-atendimento',
        [$tipoAtendimentoController, 'criar']
    );

    $router->put(
        '/api/tipos-atendimento/{id}',
        [$tipoAtendimentoController, 'atualizar']
    );

    $router->delete(
        '/api/tipos-atendimento/{id}',
        [$tipoAtendimentoController, 'excluir']
    );
    // Balcões
    $router->get(
        '/api/balcoes',
        [$balcaoController, 'listar']
    );

    $router->post(
        '/api/balcoes',
        [$balcaoController, 'criar']
    );

    $router->put(
        '/api/balcoes/{id}',
        [
            $balcaoController,
            'atualizar'
        ]
    );

    $router->delete(
        '/api/balcoes/{id}',
        [$balcaoController, 'excluir']
    );



    // Senhas
    $router->get(
        '/api/senhas',
        [$senhaController, 'listar']
    );
    
    $router->get(
        '/api/senhas/{codigo}',
        [$senhaController, 'consultar']
    );
    
    $router->post(
        '/api/senhas',
        [$senhaController, 'emitir']
    );

    
    $router->post(
        '/api/senhas/chamar-proxima',
        [$senhaController, 'chamarProxima']
    );

    $router->post(
        '/api/senhas/iniciar-atendimento',
        [$senhaController, 'iniciarAtendimento']
    );

    $router->post(
        '/api/senhas/finalizar-atendimento',
        [$senhaController, 'finalizarAtendimento']
    );
}
