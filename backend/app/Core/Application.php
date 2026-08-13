<?php

namespace App\Core;

use Dotenv\Dotenv;

use App\Config\Database;

use App\Controllers\AuthController;
use App\Controllers\BalcaoController;
use App\Controllers\SenhaController;
use App\Controllers\TipoAtendimentoController;
use App\Controllers\UtilizadorController;
use App\Controllers\DashboardController;

use App\Middleware\AuthMiddleware;

use App\Repositories\BalcaoRepository;
use App\Repositories\SenhaRepository;
use App\Repositories\TipoAtendimentoRepository;
use App\Repositories\UtilizadorRepository;

use App\Services\AuthService;
use App\Services\BalcaoService;
use App\Services\SenhaService;
use App\Services\TipoAtendimentoService;
use App\Services\UtilizadorService;
use App\Services\DashboardService;


// Responsável por montar e disponibilizar as dependências da aplicação
class Application
{
    private Router $router;

    private TipoAtendimentoController $tipoAtendimentoController;
    private BalcaoController $balcaoController;
    private SenhaController $senhaController;
    private UtilizadorController $utilizadorController;
    private AuthController $authController;
    private DashboardController $dashboardController;

    private AuthMiddleware $authMiddleware;


    public function __construct()
    {
        // Carrega as variáveis do ficheiro .env
        $dotenv = Dotenv::createImmutable(
            dirname(__DIR__, 2)
        );

        // Permite que a aplicação funcione mesmo sem .env
        $dotenv->safeLoad();


        // Cria o Router da aplicação
        $this->router = new Router();


        // Cria a conexão com o banco de dados
        $connection = Database::getConnection();


        // REPOSITORIES

        $repositoryTipoAtendimento =
            new TipoAtendimentoRepository($connection);

        $repositoryBalcao =
            new BalcaoRepository($connection);

        $repositorySenha =
            new SenhaRepository($connection);

        $repositoryUtilizador =
            new UtilizadorRepository($connection);


        // SERVICES

        $serviceTipoAtendimento =
            new TipoAtendimentoService(
                $repositoryTipoAtendimento
            );

        $serviceBalcao =
            new BalcaoService(
                $repositoryBalcao
            );

        $serviceSenha =
            new SenhaService(
                $repositorySenha
            );

        $serviceUtilizador =
            new UtilizadorService(
                $repositoryUtilizador
            );

        // Service responsável pela autenticação e JWT
        $serviceAuth =
            new AuthService(
                $repositoryUtilizador
            );

        // Cria o Service responsável pelo Dashboard
        $serviceDashboard = new DashboardService(
            $repositoryUtilizador,
            $repositoryBalcao
        );

        // CONTROLLERS

        $this->tipoAtendimentoController =
            new TipoAtendimentoController(
                $serviceTipoAtendimento
            );


        // BalcaoController precisa do BalcaoService
        // e do AuthService para gerar o JWT com o balcão
        $this->balcaoController =
            new BalcaoController(
                $serviceBalcao,
                $serviceAuth
            );


        $this->senhaController =
            new SenhaController(
                $serviceSenha
            );


        $this->utilizadorController =
            new UtilizadorController(
                $serviceUtilizador
            );


        $this->authController =
            new AuthController(
                $serviceAuth
            );

        $this->dashboardController = new DashboardController(
            $serviceDashboard
        );


        // MIDDLEWARES

        $this->authMiddleware =
            new AuthMiddleware();
    }


    // Retorna a instância do Router
    public function router(): Router
    {
        return $this->router;
    }


    // Retorna o Controller de Tipos de Atendimento
    public function tipoAtendimentoController(): TipoAtendimentoController
    {
        return $this->tipoAtendimentoController;
    }


    // Retorna o Controller de Balcões
    public function balcaoController(): BalcaoController
    {
        return $this->balcaoController;
    }


    // Retorna o Controller de Senhas
    public function senhaController(): SenhaController
    {
        return $this->senhaController;
    }


    // Retorna o Controller de Utilizadores
    public function utilizadorController(): UtilizadorController
    {
        return $this->utilizadorController;
    }


    // Retorna o Controller de Autenticação
    public function authController(): AuthController
    {
        return $this->authController;
    }

    
    // Retorna o Controller do Dashboard
    public function dashboardController(): DashboardController
    {
        return $this->dashboardController;
    }

    // Retorna o Middleware de autenticação
    public function authMiddleware(): AuthMiddleware
    {
        return $this->authMiddleware;
    }
}
