<?php

namespace App\Services;

use App\Repositories\UtilizadorRepository;
use App\Repositories\BalcaoRepository;
use InvalidArgumentException;

// Responsável pelas regras de negócio do Dashboard
class DashboardService
{
    private UtilizadorRepository $utilizadorRepository;

    private BalcaoRepository $balcaoRepository;


    // Recebe os repositories por injeção de dependência
    public function __construct(
        UtilizadorRepository $utilizadorRepository,
        BalcaoRepository $balcaoRepository
    ) {
        $this->utilizadorRepository = $utilizadorRepository;
        $this->balcaoRepository = $balcaoRepository;
    }


    // Obtém os dados necessários para o Dashboard
    public function obter(int $utilizadorId, ?int $balcaoId): array
    {
        // Busca o utilizador autenticado pelo ID presente no JWT
        $utilizador = $this->utilizadorRepository->consultar(
            $utilizadorId
        );


        // Verifica se o utilizador ainda existe
        if ($utilizador === null) {
            throw new InvalidArgumentException(
                'Utilizador não encontrado.'
            );
        }


        // Inicialmente o utilizador pode não possuir balcão
        $balcao = null;


        // Só consulta o balcão se existir um balcaoId no JWT
        if ($balcaoId !== null) {

            $balcao = $this->balcaoRepository->buscarPorId(
                $balcaoId
            );


            // O JWT possui um balcão que já não existe
            if ($balcao === null) {
                throw new InvalidArgumentException(
                    'O balcão associado ao utilizador não existe.'
                );
            }
        }


        // Retorna somente os dados necessários para o Dashboard
        return [
            'utilizador' => [
                'id' => $utilizador->getId(),
                'nome' => $utilizador->getNome(),
                'nomeUtilizador' => $utilizador->getNomeUtilizador(),
                'perfil' => $utilizador->getPerfil(),
                'criadoEm' => $utilizador->getCriadoEm()
            ],

            'balcao' => $balcao !== null
                ? [
                    'id' => $balcao->getId(),
                    'numero' => $balcao->getNumero(),
                    'nome' => $balcao->getNome()
                ]
                : null
        ];
    }
}