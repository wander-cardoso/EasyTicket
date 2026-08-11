<?php

namespace App\Core;

use App\Responses\JsonResponse;
use App\Middleware\ProfileMiddleware;

// Responsável por registar e executar as rotas da aplicação
class Router
{
    // Lista de rotas registadas
    private array $routes = [];

    // Middleware responsável pela autorização por perfil
    private ProfileMiddleware $profileMiddleware;

    public function __construct()
    {
        $this->profileMiddleware = new ProfileMiddleware();
    }

    // Registra uma rota POST
    public function post(
        string $uri,
        array $action,
        ?object $middleware = null,
        array $perfisPermitidos = []
    ): void {
        $this->add(
            'POST',
            $uri,
            $action,
            $middleware,
            $perfisPermitidos
        );
    }

    // Registra uma rota GET
    public function get(
        string $uri,
        array $action,
        ?object $middleware = null,
        array $perfisPermitidos = []
    ): void {
        $this->add(
            'GET',
            $uri,
            $action,
            $middleware,
            $perfisPermitidos
        );
    }

    // Registra uma rota PUT
    public function put(
        string $uri,
        array $action,
        ?object $middleware = null,
        array $perfisPermitidos = []
    ): void {
        $this->add(
            'PUT',
            $uri,
            $action,
            $middleware,
            $perfisPermitidos
        );
    }

    // Registra uma rota DELETE
    public function delete(
        string $uri,
        array $action,
        ?object $middleware = null,
        array $perfisPermitidos = []
    ): void {
        $this->add(
            'DELETE',
            $uri,
            $action,
            $middleware,
            $perfisPermitidos
        );
    }

    // Adiciona uma rota à lista
    private function add(
        string $method,
        string $uri,
        array $action,
        ?object $middleware = null,
        array $perfisPermitidos = []
    ): void {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware,
            'perfisPermitidos' => $perfisPermitidos
        ];
    }

    // Procura e executa a rota correspondente
    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {

            // Verifica se o método HTTP é o mesmo
            if ($route['method'] !== $request->method()) {
                continue;
            }

            // Verifica se a URL corresponde à rota
            $parameters = $this->matchRoute(
                $route['uri'],
                $request->uri()
            );

            // Se não encontrou correspondência, continua procurando
            if ($parameters === false) {
                continue;
            }

            // AUTENTICAÇÃO

            // Executa o middleware quando a rota exigir autenticação
            if ($route['middleware'] !== null) {

                // Valida o JWT
                $usuarioAutenticado =
                    $route['middleware']->verificar();

                // Guarda o utilizador autenticado na requisição
                $request->definirUtilizadorAutenticado(
                    $usuarioAutenticado
                );


                // AUTORIZAÇÃO

                // Verifica se a rota exige algum perfil específico
                if ($route['perfisPermitidos'] !== []) {

                    $this->profileMiddleware->verificar(
                        $usuarioAutenticado,
                        $route['perfisPermitidos']
                    );
                }
            }

            // Obtém o Controller
            $controller = $route['action'][0];

            // Obtém o método que será executado
            $method = $route['action'][1];

            // Analisa os parâmetros necessários pelo Controller
            $reflection = new \ReflectionMethod(
                $controller,
                $method
            );

            // Lista de argumentos que serão enviados ao Controller
            $arguments = [];

            foreach ($reflection->getParameters() as $parameter) {

                // Obtém o tipo do parâmetro
                $type = $parameter->getType();

                // Se o Controller precisar da Request, injeta automaticamente o objeto.
                if (
                    $type instanceof \ReflectionNamedType &&
                    $type->getName() === Request::class
                ) {
                    $arguments[] = $request;
                    continue;
                }

                // Obtém o nome do parâmetro
                $name = $parameter->getName();

                //Procura o parâmetro capturado na URL
                if (isset($parameters[$name])) {
                    $arguments[] = $parameters[$name];
                    continue;
                }

                // Parâmetro obrigatório não encontrado
                JsonResponse::error(
                    'Parâmetro obrigatório não informado.',
                    400
                );

                return;
            }

            // Executa o método do Controller
            $controller->$method(...$arguments);

            return;
        }

        // Nenhuma rota encontrada
        JsonResponse::error(
            'Rota não encontrada.',
            404
        );
    }

    // Verifica se uma rota corresponde à URL informada
    private function matchRoute(
        string $route,
        string $uri
    ): array|false {

        // Transforma parâmetros como {id} em grupos de captura Regex nomeados
        $pattern = preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '(?P<$1>[^/]+)',
            $route
        );

        // Garante correspondência exata do início ao fim da URL
        $pattern = '#^' . $pattern . '$#';

        // Verifica se a URL corresponde ao padrão
        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        // Mantém apenas os parâmetros nomeados
        return array_filter(
            $matches,
            'is_string',
            ARRAY_FILTER_USE_KEY
        );
    }
}