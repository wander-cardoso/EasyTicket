<?php

namespace App\Models;

use JsonSerializable;

// Representa uma senha do sistema
class Senha implements JsonSerializable
{
    private ?int $id;

    private string $codigo;

    private ?string $nomeCliente;

    private ?string $telefoneContacto;

    private int $tipoAtendimentoId;

    private ?int $balcaoId;

    private string $status;

    private string $dataEmissao;

    private ?string $dataChamada;

    private ?string $dataInicioAtendimento;

    private ?string $dataFinalizacao;

    public function __construct(
        ?int $id,
        string $codigo,
        ?string $nomeCliente,
        ?string $telefoneContacto,
        int $tipoAtendimentoId,
        ?int $balcaoId,
        string $status,
        string $dataEmissao,
        ?string $dataChamada,
        ?string $dataInicioAtendimento,
        ?string $dataFinalizacao
    ) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->nomeCliente = $nomeCliente;
        $this->telefoneContacto = $telefoneContacto;
        $this->tipoAtendimentoId = $tipoAtendimentoId;
        $this->balcaoId = $balcaoId;
        $this->status = $status;
        $this->dataEmissao = $dataEmissao;
        $this->dataChamada = $dataChamada;
        $this->dataInicioAtendimento = $dataInicioAtendimento;
        $this->dataFinalizacao = $dataFinalizacao;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nomeCliente' => $this->nomeCliente,
            'telefoneContacto' => $this->telefoneContacto,
            'tipoAtendimentoId' => $this->tipoAtendimentoId,
            'balcaoId' => $this->balcaoId,
            'status' => $this->status,
            'dataEmissao' => $this->dataEmissao,
            'dataChamada' => $this->dataChamada,
            'dataInicioAtendimento' => $this->dataInicioAtendimento,
            'dataFinalizacao' => $this->dataFinalizacao
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNomeCliente(): ?string
    {
        return $this->nomeCliente;
    }

    public function setNomeCliente(?string $nomeCliente): void
    {
        $this->nomeCliente = $nomeCliente;
    }

    public function getTelefoneContacto(): ?string
    {
        return $this->telefoneContacto;
    }

    public function setTelefoneContacto(?string $telefoneContacto): void
    {
        $this->telefoneContacto = $telefoneContacto;
    }

    public function getTipoAtendimentoId(): int
    {
        return $this->tipoAtendimentoId;
    }

    public function getBalcaoId(): ?int
    {
        return $this->balcaoId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDataEmissao(): string
    {
        return $this->dataEmissao;
    }

    public function getDataChamada(): ?string
    {
        return $this->dataChamada;
    }

    public function getDataInicioAtendimento(): ?string
    {
        return $this->dataInicioAtendimento;
    }

    public function getDataFinalizacao(): ?string
    {
        return $this->dataFinalizacao;
    }
}
