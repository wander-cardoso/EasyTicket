<?php

namespace App\Core;

use Dotenv\Dotenv;
use App\Config\Database;
use App\Controllers\BalcaoController;
use App\Controllers\SenhaController;
use App\Core\Router;
use App\Repositories\TipoAtendimentoRepository;
use App\Services\TipoAtendimentoService;
use App\Controllers\TipoAtendimentoController;
use App\Repositories\BalcaoRepository;
use App\Repositories\SenhaRepository;
use App\Services\BalcaoService;
use App\Services\SenhaService;
use App\Controllers\UtilizadorController;
use App\Repositories\UtilizadorRepository;
use App\Services\UtilizadorService;
use App\Controllers\AuthController;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
class Application
{
    private Router $router;

    private TipoAtendimentoController $tipoAtendimentoController;
    private BalcaoController $balcaoController;
    private SenhaController $senhaController;
    private UtilizadorController $utilizadorController;
    private AuthController $authController;
    private AuthMiddleware $authMiddleware;

    public function __construct()
    {
        // Carrega as variáveis do ficheiro .env
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));

        // Para não gerar erro caso o .env não exista
        $dotenv->safeLoad();

        // Cria o Router da aplicação
        $this->router = new Router();

        // Cria a conexão com o banco
        $connection = Database::getConnection();

        // Objeto do Repository
        $repositoryTipoAtendimento = new TipoAtendimentoRepository($connection);
        // Objeto do Service
        $serviceTipoAtendimemto = new TipoAtendimentoService($repositoryTipoAtendimento);
        // Objeto do Controller
        $this->tipoAtendimentoController = new TipoAtendimentoController(
            $serviceTipoAtendimemto
        );

        // Objeto do Repository
        $repositoryBalcao = new BalcaoRepository($connection);
        // Objeto do Service
        $serviceBalcao = new BalcaoService($repositoryBalcao);
        // Objeto do Controller
        $this->balcaoController = new BalcaoController($serviceBalcao);

        // Objeto do Repository
        $repositorySenha = new SenhaRepository($connection);
        // Objeto do Service
        $serviceSenha = new SenhaService($repositorySenha);
        // Objeto do Controller
        $this->senhaController = new SenhaController($serviceSenha);

        // Objeto do Repository
        $repositoryUtilizador = new UtilizadorRepository($connection);
        // Objeto do Service
        $serviceUtilizador = new UtilizadorService($repositoryUtilizador);
        // Objeto do Controller
        $this->utilizadorController = new UtilizadorController(
            $serviceUtilizador
        );

        // Objeto do Service de autenticação
        $serviceAuth = new AuthService($repositoryUtilizador);
        // Objeto do Controller de autenticação
        $this->authController = new AuthController($serviceAuth);


        // Cria o Middleware de autenticação
        $this->authMiddleware = new AuthMiddleware();
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
    // Retorna o Controller do Balcao
    public function balcaoContrller(): BalcaoController
    {
        return $this->balcaoController;
    }
    // Retorna o Controller da Senha
    public function senhaController(): SenhaController
    {
        return $this->senhaController;
    }
    // Retorna o Controller do Utilizador
    public function utilizadorController(): UtilizadorController
    {
        return $this->utilizadorController;
    }
    // Retorna o Controller do Utilizador
    public function authController(): AuthController
    {
        return $this->authController;
    }

    // Retorna o Middleware de autenticação
    public function authMiddleware(): AuthMiddleware
    {
        return $this->authMiddleware;
    }

    
}
