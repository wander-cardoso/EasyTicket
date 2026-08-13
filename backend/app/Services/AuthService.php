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
    public function __construct(
        UtilizadorRepository $repository
    ) {
        $this->repository = $repository;
    }


    // Autentica o utilizador e gera o token JWT
    public function autenticar(
        string $nomeUtilizador,
        string $password
    ): array {

        // Valida o nome de utilizador
        if (trim($nomeUtilizador) === '') {
            throw new InvalidArgumentException(
                'Informe o nome de utilizador.'
            );
        }


        // Remove espaços desnecessários
        // e converte para letras minúsculas
        $nomeUtilizador = mb_strtolower(
            trim($nomeUtilizador),
            'UTF-8'
        );


        // Valida a password
        if (trim($password) === '') {
            throw new InvalidArgumentException(
                'Informe a password.'
            );
        }


        // Garante um comprimento mínimo
        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException(
                'A password deve ter pelo menos 8 caracteres.'
            );
        }


        // Procura o utilizador no banco
        $utilizador =
            $this->repository->buscarPorNomeUtilizador(
                $nomeUtilizador
            );


        // Não revela se o problema foi o utilizador
        // ou a password
        if (
            $utilizador === null ||
            !password_verify(
                $password,
                $utilizador->getPassword()
            )
        ) {
            throw new InvalidArgumentException(
                'Nome de utilizador ou password inválidos.'
            );
        }


        // Gera o JWT sem balcão selecionado
        $token = $this->gerarToken(
            $utilizador->getId(),
            $utilizador->getNomeUtilizador(),
            $utilizador->getPerfil()
        );


        // Retorna o token e os dados públicos
        // do utilizador
        return [
            'token' => $token,

            'utilizador' => [
                'id' => $utilizador->getId(),
                'nome' => $utilizador->getNome(),
                'nomeUtilizador' =>
                $utilizador->getNomeUtilizador(),
                'perfil' => $utilizador->getPerfil()
            ]
        ];
    }


    // Busca o utilizador pelo ID e gera um novo JWT
    // contendo o balcão selecionado
    public function gerarTokenComBalcaoPorId(
        int $utilizadorId,
        int $balcaoId
    ): string {

        // Busca o utilizador através do ID
        // presente no JWT
        $utilizador =
            $this->repository->consultar(
                $utilizadorId
            );


        // Verifica se o utilizador ainda existe
        if ($utilizador === null) {
            throw new InvalidArgumentException(
                'Utilizador não encontrado.'
            );
        }


        // Gera um novo JWT contendo o balcão
        return $this->gerarToken(
            $utilizadorId,
            $utilizador->getNomeUtilizador(),
            $utilizador->getPerfil(),
            $balcaoId
        );
    }


    // Centraliza a criação do JWT
    private function gerarToken(
        int $utilizadorId,
        string $nomeUtilizador,
        string $perfil,
        ?int $balcaoId = null
    ): string {

        // Obtém a chave secreta utilizada
        // para assinar o JWT
        $chave = $_ENV['JWT_SECRET'] ?? '';


        if ($chave === '') {
            throw new \RuntimeException(
                'JWT_SECRET não configurado.'
            );
        }


        // Define os dados armazenados no JWT
        $payload = [
            'iss' => 'easyticketpartteam',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $utilizadorId,
            'nomeUtilizador' => $nomeUtilizador,
            'perfil' => $perfil
        ];


        // Adiciona o balcão somente quando existir
        if ($balcaoId !== null) {
            $payload['balcaoId'] = $balcaoId;
        }


        // Gera o JWT
        return JWT::encode(
            $payload,
            $chave,
            'HS256'
        );
    }
}
