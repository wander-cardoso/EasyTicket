<?php

namespace App\Core;

use Dotenv\Dotenv;
use App\Config\Database;
use App\Core\Router;
use App\Repositories\TipoAtendimentoRepository;
use App\Services\TipoAtendimentoService;
use App\Controllers\TipoAtendimentoController;

class Application
{
    private Router $router;

    private TipoAtendimentoController $tipoAtendimentoController;

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

        // Cria o Repository
        $repository = new TipoAtendimentoRepository($connection);

        // Cria o Service
        $service = new TipoAtendimentoService($repository);

        // Cria o Controller
        $this->tipoAtendimentoController = new TipoAtendimentoController(
            $service
        );
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
}