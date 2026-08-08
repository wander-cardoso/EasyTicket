<?php

namespace App\Models;

// Representa um Tipo de Atendimento do sistema
class TipoAtendimento implements \JsonSerializable
{

    private ?int $id;

    private string $nome;

    private string $sigla;

    private string $descricao;

    public function __construct(
        ?int $id,
        string $nome,
        string $sigla,
        string $descricao
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->sigla = $sigla;
        $this->descricao = $descricao;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'descricao' => $this->descricao
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getSigla(): string
    {
        return $this->sigla;
    }

    public function setSigla(string $sigla): void
    {
        $this->sigla = $sigla;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }
}
