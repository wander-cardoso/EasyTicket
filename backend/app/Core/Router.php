<?php

namespace App\Core;

use App\Responses\JsonResponse;

// Responsável por registar e executar as rotas da aplicação
class Router
{

    // Lista de rotas registadas
    private array $routes = [];

    // Regista uma rota GET
    public function get(string $uri, array $action): void
    {
        $this->add('GET', $uri, $action);
    }

    // Registra uma rota POST
    public function post(string $uri, array $action): void
    {
        $this->add('POST', $uri, $action);
    }

    // Registra uma rota PUT
    public function put(string $uri, array $action): void
    {
        $this->add('PUT', $uri, $action);
    }

    //Registra uma rota DELET
    public function delete(string $uri, array $action): void
    {
        $this->add('DELETE', $uri, $action);
    }

    // Adiciona uma rota à lista
    private function add(
        string $method,
        string $uri,
        array $action
    ): void {

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

// Procura e executa a rota correspondente
public function dispatch(Request $request): void
{
    foreach ($this->routes as $route) {

        // Primeiro verifica se o método HTTP é o mesmo
        if ($route['method'] !== $request->method()) {
            continue;
        }

        // Verifica se a URL corresponde à rota e captura os parâmetros
        $parameters = $this->matchRoute(
            $route['uri'],
            $request->uri()
        );

        // Se não encontrou correspondência, continua procurando
        if ($parameters === false) {
            continue;
        }

        // Obtém o Controller
        $controller = $route['action'][0];

        // Obtém o método que será executado
        $method = $route['action'][1];

        // Obtém os parâmetros necessários pelo método do Controller
        $reflection = new \ReflectionMethod($controller, $method);

        // Lista de argumentos que serão enviados ao Controller
        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {

            // Obtém o tipo do parâmetro
            $type = $parameter->getType();

            // Se o método precisar de Request, injeta o objeto Request
            if (
                $type instanceof \ReflectionNamedType &&
                $type->getName() === Request::class
            ) {
                $arguments[] = $request;
                continue;
            }

            // Caso contrário, procura o parâmetro capturado na URL
            $name = $parameter->getName();

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

        // Executa o método com os argumentos necessários
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

        //Transforma parâmetros amigáveis como {id} em grupos de captura Regex nomeados
        $pattern = preg_replace(
        '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
        '(?P<$1>[^/]+)',
        $route
    );
// Delimitadores e travas do inicio ao fim para correspondencia exata
    $pattern = '#^' . $pattern . '$#';

    // Verifica se a URL corresponde ao padrão da minha API
    if (!preg_match($pattern, $uri, $matches)) {
        return false;
    }

    // Atravez do filtro mantém apenas os parâmetros nomeados
    return array_filter(
        $matches,
        'is_string',
        ARRAY_FILTER_USE_KEY
    );
}
}