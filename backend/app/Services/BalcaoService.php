<?php

namespace App\Services;

use App\Models\Balcao;
use App\Repositories\BalcaoRepository;

// Classe responsável pelas regras de negócio dos Balcões
class BalcaoService
{
    private BalcaoRepository $repository;

    // Recebe o Repository por injeção de dependência
    public function __construct(BalcaoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Retorna todos os balcões
    /** @return Balcao[] */
    public function listar(): array
    {
        return $this->repository->listar();
    }

    // Cria um novo balcão
    public function criar(Balcao $balcao): bool
    {
        // Verifica se o número já está cadastrado
        if ($this->repository->numeroExiste($balcao->getNumero())) {
            throw new \InvalidArgumentException(
                'O número do balcão já existe. Escolha outro número.'
            );
        }

        // Verifica se o nome já está cadastrado
        if ($this->repository->nomeExiste($balcao->getNome())) {
            throw new \InvalidArgumentException(
                'O nome do balcão já existe. Escolha outro nome.'
            );
        }

        // Todas as regras foram satisfeitas
        return $this->repository->criar($balcao);
    }

    // Atualiza um balcão
    public function atualizar(
        int $id,
        Balcao $balcao
    ): bool {

        // Verifica se o número já pertence a outro balcão
        if (
            $this->repository->numeroExiste(
                $balcao->getNumero(),
                $id
            )
        ) {
            throw new \InvalidArgumentException(
                'O número do balcão já existe. Escolha outro número.'
            );
        }

        // Verifica se o nome já pertence a outro balcão
        if (
            $this->repository->nomeExiste(
                $balcao->getNome(),
                $id
            )
        ) {
            throw new \InvalidArgumentException(
                'O nome do balcão já existe. Escolha outro nome.'
            );
        }

        // Todas as regras foram satisfeitas
        return $this->repository->atualizar(
            $id,
            $balcao
        );
    }

    // Exclui um balcão
    public function excluir(int $id): bool
    {
        return $this->repository->excluir($id);
    }
}
