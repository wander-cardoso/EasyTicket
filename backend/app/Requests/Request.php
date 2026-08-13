<?php

namespace App\Requests;

// Classe que centraliza o acesso às informações da requisição
class Request
{
    private array $body;

    // Guarda os dados do utilizador autenticado
    private ?array $utilizadorAutenticado = null;


    // Assim que a classe é criada, o corpo da requisição é lido apenas uma vez
    public function __construct()
    {
        // Lê o corpo bruto da requisição
        $content = file_get_contents('php://input');

        // Converte o JSON em um array
        $this->body = json_decode($content, true) ?? [];
    }

    // Retorna o método HTTP utilizado
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    // Retorna apenas o caminho da URL
    public function uri(): string
    {
        return parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );
    }

    // Retorna todos os dados enviados no corpo da requisição
    public function all(): array
    {
        return $this->body;
    }

    // Retorna um único campo do JSON
    public function input(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->body[$key] ?? $default;
    }

    // Guarda os dados do utilizador autenticado
    public function definirUtilizadorAutenticado(
        array $utilizador
    ): void {
        $this->utilizadorAutenticado = $utilizador;
    }

    // Retorna os dados do utilizador autenticado
    public function utilizadorAutenticado(): ?array
    {
        return $this->utilizadorAutenticado;
    }

}
