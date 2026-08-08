<?php

// Carrega o autoload do Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Core\Request;
use function App\Routes\registerRoutes;


$app = new Application();

$router = $app->router();
$tipoAtendimentoController = $app->tipoAtendimentoController();

// Regista todas as rotas da API
require_once dirname(__DIR__) . '/app/Routes/Api.php';

registerRoutes(
    $router,
    $tipoAtendimentoController
);

$request = new Request();

// Executa a rota correspondente
$router->dispatch($request);