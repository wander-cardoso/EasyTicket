<?php

namespace App\Routes;

use App\Controllers\AuthController;
use App\Core\Router;
use App\Controllers\BalcaoController;
use App\Controllers\DashboardController;
use App\Controllers\SenhaController;
use App\Controllers\TipoAtendimentoController;
use App\Controllers\UtilizadorController;
use App\Middleware\AuthMiddleware;

// Registra todas as rotas da API.
function registerRoutes(
    Router $router,
    TipoAtendimentoController $tipoAtendimentoController,
    BalcaoController $balcaoController,
    SenhaController $senhaController,
    UtilizadorController $utilizadorController,
    DashboardController $dashboardController,
    AuthController $authController,
    AuthMiddleware $authMiddleware
): void {

    // AUTENTICAÇÃO

    // Login é público
    $router->post(
        '/api/login',
        [$authController, 'login']
    );

    
    // UTILIZADORES

    // Dashboard
    $router->get(
        '/api/me/dashboard',
        [$dashboardController, 'obter'],
        $authMiddleware
    );

    // Apenas GESTOR pode criar utilizadores
    $router->post(
        '/api/utilizadores',
        [$utilizadorController, 'criar'],
        $authMiddleware,
        ['GESTOR']
    );

    // Apenas GESTOR pode editar outros utilizadores
    $router->put(
        '/api/utilizadores/{id}',
        [$utilizadorController, 'editar'],
        $authMiddleware,
        ['GESTOR']
    );
    // Para que o OPERADOR consiga editar seus dados
    $router->put(
        '/api/me',
        [$utilizadorController, 'atualizarProprioPerfil'],
        $authMiddleware
    );

    // Apenas GESTOR pode listar utilizadores
    $router->get(
        '/api/utilizadores',
        [$utilizadorController, 'listar'],
        $authMiddleware,
        ['GESTOR']
    );

    // Rota de ambos PERFIS
    $router->get(
        '/api/me',
        [$utilizadorController, 'me'],
        $authMiddleware
    );

    // Apenas GESTOR pode consultar utilizador por ID
    $router->get(
        '/api/utilizadores/{id}',
        [$utilizadorController, 'consultar'],
        $authMiddleware,
        ['GESTOR']
    );


    // TIPOS DE ATENDIMENTO

    // Público
    $router->get(
        '/api/tipos-atendimento',
        [$tipoAtendimentoController, 'listar']
    );

    // Apenas GESTOR pode criar
    $router->post(
        '/api/tipos-atendimento',
        [$tipoAtendimentoController, 'criar'],
        $authMiddleware,
        ['GESTOR']
    );

    // Apenas GESTOR pode editar
    $router->put(
        '/api/tipos-atendimento/{id}',
        [$tipoAtendimentoController, 'atualizar'],
        $authMiddleware,
        ['GESTOR']
    );

    // Apenas GESTOR pode excluir
    $router->delete(
        '/api/tipos-atendimento/{id}',
        [$tipoAtendimentoController, 'excluir'],
        $authMiddleware,
        ['GESTOR']
    );


    // BALCÕES

    // Público
    $router->get(
        '/api/balcoes',
        [$balcaoController, 'listar']
    );

    // Opcao o escolher o balcao
    $router->post(
        '/api/balcoes/selecionar',
        [$balcaoController, 'selecionar'],
        $authMiddleware
    );

    // Apenas GESTOR pode criar
    $router->post(
        '/api/balcoes',
        [$balcaoController, 'criar'],
        $authMiddleware,
        ['GESTOR']
    );

    // Apenas GESTOR pode editar
    $router->put(
        '/api/balcoes/{id}',
        [$balcaoController, 'atualizar'],
        $authMiddleware,
        ['GESTOR']
    );

    // Apenas GESTOR pode excluir
    $router->delete(
        '/api/balcoes/{id}',
        [$balcaoController, 'excluir'],
        $authMiddleware,
        ['GESTOR']
    );


    // SENHAS

    // Público
    $router->post(
        '/api/senhas',
        [$senhaController, 'emitir']
    );

    // Público
    $router->get(
        '/api/senhas/{codigo}',
        [$senhaController, 'consultar']
    );

    // GESTOR e OPERADOR
    $router->post(
        '/api/senhas/chamar-proxima',
        [$senhaController, 'chamarProxima'],
        $authMiddleware,
        ['GESTOR', 'OPERADOR']
    );

    // GESTOR e OPERADOR
    $router->post(
        '/api/senhas/iniciar-atendimento',
        [$senhaController, 'iniciarAtendimento'],
        $authMiddleware,
        ['GESTOR', 'OPERADOR']
    );

    // GESTOR e OPERADOR
    $router->post(
        '/api/senhas/finalizar-atendimento',
        [$senhaController, 'finalizarAtendimento'],
        $authMiddleware,
        ['GESTOR', 'OPERADOR']
    );
}
