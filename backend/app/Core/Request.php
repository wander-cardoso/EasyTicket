<?php

namespace App\Core;

// Classe que centraliza o acesso às informações da requisição
class Request
{
    private array $body;

    // Assim que a classe é criada, o corpo da requisição é lido apenas uma vez
    public function __construct()
    {
        // Lê o corpo bruto da requisição
        $content = file_get_contents('php://input');

        // Converte o JSON em um array
        $this->body = json_decode($content, true) ?? [];
    }

    // Esse metodo retorna o tipo do HTTP utilizado(GET POST PUT DELETE)
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    //Retorna apenas o caminho da URL.
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

    // Retorna um único campo do JSON(possivel remocao)
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }
}