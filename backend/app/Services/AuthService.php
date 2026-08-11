<?php

namespace App\Services;

use App\Repositories\UtilizadorRepository;
use Firebase\JWT\JWT;
use InvalidArgumentException;

// Responsável pelas regras de autenticação dos utilizadores
class AuthService
{
    private UtilizadorRepository $repository;

    // Recebe o Repository por injeção de dependência
    public function __construct(UtilizadorRepository $repository)
    {
        $this->repository = $repository;
    }

    // Autentica o utilizador e gera o token JWT
    public function autenticar(string $nomeUtilizador, string $password): array
    {
        // Valida o nome de utilizador
        if (trim($nomeUtilizador) === '') {
            throw new InvalidArgumentException(
                'Informe o nome de utilizador.'
            );
        }

        // Remove espaços desnecessários e converte para letras minúsculas
        $nomeUtilizador = mb_strtolower(trim($nomeUtilizador), 'UTF-8');
        // Valida a password
        if (trim($password) === '') {
            throw new InvalidArgumentException(
                'Informe a password.'
            );
        }

        // Garante um comprimento mínimo seguro (ex: 8 caracteres)
        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException(
                'A password deve ter pelo menos 8 caracteres.'
            );
        }

        // Valida a password
        if ($password === '') {
            throw new InvalidArgumentException(
                'Informe a password.'
            );
        }


        // Procura o login de utilizador no banco
        $utilizador = $this->repository->buscarPorNomeUtilizador(
            $nomeUtilizador
        );

        // Não revela se o problema foi o utilizador ou a password
        if ($utilizador === null || !password_verify(
            $password, $utilizador->getPassword() )) {
            throw new InvalidArgumentException(
                'Nome de utilizador ou password inválidos.'
            );
        }

        // Obtém a chave secreta utilizada para assinar o token
        $chave = $_ENV['JWT_SECRET'] ?? '';

        if ($chave === '') {
            throw new \RuntimeException(
                'JWT_SECRET não configurado.'
            );
        }

        // Define os dados que serão armazenados no token
        $payload = [
            'iss' => 'easyticketpartteam',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $utilizador->getId(),
            'nomeUtilizador' => $utilizador->getNomeUtilizador(),
            'perfil' => $utilizador->getPerfil()
        ];

        // Gera o token JWT
        $token = JWT::encode(
            $payload,
            $chave,
            'HS256'
        );

        // Retorna o token e os dados públicos do utilizador
        return [
            'token' => $token,
            'utilizador' => [
                'id' => $utilizador->getId(),
                'nome' => $utilizador->getNome(),
                'nomeUtilizador' => $utilizador->getNomeUtilizador(),
                'perfil' => $utilizador->getPerfil()
            ]
        ];
    }
}