<?php

namespace App\Models;

// Representa os Balcoes de atendimento do sistema
class Balcao implements \JsonSerializable
{

    private ?int $id;
    private ?int $numero;
    private string $nome;
    private int $tipoAtendimentoId;

    public function __construct(
    ?int $id,
    int $numero,
    string $nome,
    int $tipoAtendimentoId
) {
    $this->id = $id;
    $this->numero = $numero;
    $this->nome = $nome;
    $this->tipoAtendimentoId = $tipoAtendimentoId;
}


    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'numero' => $this->numero,
            'tipoAtendimentoId' => $this->tipoAtendimentoId
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function setNumero(string $numero): void
    {
        $this->numero = $numero;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getTipoAtendimentoId(): int
{
    return $this->tipoAtendimentoId;
}

public function setTipoAtendimentoId(
    int $tipoAtendimentoId
): void {
    $this->tipoAtendimentoId = $tipoAtendimentoId;
}
}
