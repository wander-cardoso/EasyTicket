<?php

// Permite que o Angular acesse a API durante o desenvolvimento
header('Access-Control-Allow-Origin: http://localhost:4200');

// Permite os métodos HTTP utilizados pela API
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Permite os cabeçalhos utilizados pela API
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Trata requisições de verificação do navegador
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Carrega o autoload do Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Requests\Request;

use function App\Routes\registerRoutes;


$app = new Application();

$router = $app->router();
$tipoAtendimentoController = $app->tipoAtendimentoController();
$balcaoController = $app->balcaoController();
$senhaController = $app->senhaController();
$utilizadorController = $app->utilizadorController();
$dashboardController = $app->dashboardController();
$authController = $app->authController();
$authMiddleware = $app->authMiddleware();

// Regista todas as rotas da API
require_once dirname(__DIR__) . '/app/Routes/Api.php';

registerRoutes(
    $router,
    $tipoAtendimentoController,
    $balcaoController,
    $senhaController,
    $utilizadorController,
    $dashboardController,
    $authController,
    $authMiddleware
);

$request = new Request();

// Executa a rota correspondente
$router->dispatch($request);

