<?php

namespace App\Services;

use App\Models\Utilizador;
use App\Repositories\UtilizadorRepository;
use InvalidArgumentException;

// Responsável pelas regras de negócio dos utilizadores
class UtilizadorService
{
    private UtilizadorRepository $repository;

    // Recebe o Repository por injeção de dependência
    public function __construct(UtilizadorRepository $repository)
    {
        $this->repository = $repository;
    }

    // Cria um novo utilizador
    public function criar(
        string $nome,
        string $nomeUtilizador,
        string $password,
        string $perfil
    ): Utilizador {
        // Valida o nome
        if (trim($nome) === '') {
            throw new InvalidArgumentException(
                'Informe o nome do utilizador.'
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

        // Valida se tem Letras, Números e Caracteres Especiais
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException(
                'A password deve conter letras e números.'
            );
        }

        // Valida o perfil
        if (!in_array($perfil, ['OPERADOR', 'GESTOR'], true)) {
            throw new InvalidArgumentException(
                'Informe um perfil válido.'
            );
        }

        // Verifica se o nome de utilizador já existe
        $utilizadorExistente = $this->repository->buscarPorNomeUtilizador(
            $nomeUtilizador
        );

        if ($utilizadorExistente !== null) {
            throw new InvalidArgumentException(
                'O nome de utilizador já está em uso.'
            );
        }

        // Cria o hash seguro da password
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Cria o Model com a password já protegida
        $utilizador = new Utilizador(
            null,
            $nome,
            $nomeUtilizador,
            $passwordHash,
            $perfil
        );

        // Persiste o utilizador no banco
        return $this->repository->criar($utilizador);
    }

    // Lista todos os utilizadores
public function listar(): array
{
    return $this->repository->listar();
}

// Consulta um utilizador pelo ID
public function consultar(int $id): Utilizador
{
    if ($id <= 0) {
        throw new InvalidArgumentException(
            'ID de utilizador inválido.'
        );
    }

    $utilizador = $this->repository->consultar($id);

    if ($utilizador === null) {
        throw new InvalidArgumentException(
            'Utilizador não encontrado.'
        );
    }

    return $utilizador;
}

// Edita um utilizador existente
public function editar(
    int $id,
    string $nome,
    string $nomeUtilizador,
    string $perfil,
    ?string $password = null
): Utilizador {

    // Valida o ID
    if ($id <= 0) {
        throw new InvalidArgumentException(
            'ID de utilizador inválido.'
        );
    }

    // Valida o nome
    $nome = trim($nome);

    if ($nome === '') {
        throw new InvalidArgumentException(
            'Informe o nome do utilizador.'
        );
    }

    // Normaliza o nome de utilizador
    $nomeUtilizador = mb_strtolower(
        trim($nomeUtilizador),
        'UTF-8'
    );

    if ($nomeUtilizador === '') {
        throw new InvalidArgumentException(
            'Informe o nome de utilizador.'
        );
    }

    // Normaliza o perfil
    $perfil = mb_strtoupper(
        trim($perfil),
        'UTF-8'
    );

    // Valida o perfil
    if (!in_array($perfil, ['OPERADOR', 'GESTOR'], true)) {
        throw new InvalidArgumentException(
            'Informe um perfil válido.'
        );
    }

    // Procura o utilizador atual
    $utilizadorAtual = $this->repository->consultar($id);

    if ($utilizadorAtual === null) {
        throw new InvalidArgumentException(
            'Utilizador não encontrado.'
        );
    }

    // Verifica se o novo nome de utilizador já pertence
    // a outro utilizador
    $utilizadorExistente = $this->repository
        ->buscarPorNomeUtilizador($nomeUtilizador);

    if (
        $utilizadorExistente !== null &&
        $utilizadorExistente->getId() !== $id
    ) {
        throw new InvalidArgumentException(
            'O nome de utilizador já está em uso.'
        );
    }

    // Mantém a password atual por padrão
    $passwordHash = $utilizadorAtual->getPassword();

    // Se uma nova password foi informada, valida e gera novo hash
    if ($password !== null && $password !== '') {

        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException(
                'A password deve ter pelo menos 8 caracteres.'
            );
        }

        if (
            !preg_match('/[A-Za-z]/', $password) ||
            !preg_match('/[0-9]/', $password)
        ) {
            throw new InvalidArgumentException(
                'A password deve conter letras e números.'
            );
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
    }

    // Cria o Model atualizado
    $utilizadorAtualizado = new Utilizador(
        $id,
        $nome,
        $nomeUtilizador,
        $passwordHash,
        $perfil,
        $utilizadorAtual->getCriadoEm()
    );

    // Persiste as alterações
    return $this->repository->editar(
        $utilizadorAtualizado
    );
}
}