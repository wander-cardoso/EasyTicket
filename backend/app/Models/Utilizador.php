<?php

namespace App\Models;

// Representa um utilizador do sistema
class Utilizador
{
    private ?int $id;
    private string $nome;
    private string $nomeUtilizador;
    private string $password;
    private string $perfil;
    private ?string $criadoEm;

    public function __construct(
        ?int $id,
        string $nome,
        string $nomeUtilizador,
        string $password,
        string $perfil,
        ?string $criadoEm = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->nomeUtilizador = $nomeUtilizador;
        $this->password = $password;
        $this->perfil = $perfil;
        $this->criadoEm = $criadoEm;
    }

    // Retorna o ID do utilizador
    public function getId(): ?int
    {
        return $this->id;
    }

    // Reorna o Nome do Utilizador
    public function getNome() :string
    {
        return $this->nome;
    }

    // Persiste o nome do utilizador
    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }
    // Persiste o nome do utilizador (Login)
    public function setNomeUtilizador(
        string $nomeUtilizador
    ): void {
        $this->nomeUtilizador = $nomeUtilizador;
    }

    // Retorna o nome de utilizador
    public function getNomeUtilizador(): string
    {
        return $this->nomeUtilizador;
    }

    // Retorna o hash da password
    public function getPassword(): string
    {
        return $this->password;
    }

    // Persiste o password do utilizador
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    // Retorna o perfil do utilizador
    public function getPerfil(): string
    {
        return $this->perfil;
    }

    // Retorna a data de criação
    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }
}
