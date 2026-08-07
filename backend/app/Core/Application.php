<?php

namespace App\Core;

use Dotenv\Dotenv;

//Classe responsável por inicializar a aplicação

class Application
{
  
    private Router $router;

   
    public function __construct()
    {
        // Carrega as variáveis do ficheiro .env
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));

        // Para não gera erro caso o .env não exista
        $dotenv->safeLoad();

        // Cria o Router da aplicação
        $this->router = new Router();
    }

    //Retorna a instância do Router
    public function router(): Router
    {
        return $this->router;
    }
}