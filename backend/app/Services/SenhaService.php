<?php

namespace App\Services;

use App\Models\Senha;
use App\Repositories\SenhaRepository;
use App\Repositories\BalcaoRepository;
use InvalidArgumentException;

// Classe responsável pelas regras de negócio das Senhas
class SenhaService
{
    private SenhaRepository $repository;

    private BalcaoRepository $balcaoRepository;

    // Recebe os Repositories por injeção de dependência
    public function __construct(
        SenhaRepository $repository,
        BalcaoRepository $balcaoRepository
    ) {
        $this->repository = $repository;
        $this->balcaoRepository = $balcaoRepository;
    }


    // Retorna todas as senhas
    public function listar(
        int $pagina = 1,
        int $limite = 10
    ): array {
        return $this->repository->listar(
            $pagina,
            $limite
        );
    }


    // Consulta uma senha pelo código
    public function consultarPorCodigo(
        string $codigo
    ): ?Senha {

        if (trim($codigo) === '') {
            throw new InvalidArgumentException(
                'Informe o código da senha.'
            );
        }

        return $this->repository->consultarPorCodigo(
            $codigo
        );
    }


    // Emite uma nova senha
    public function emitirSenha(
        Senha $senha
    ): Senha {

        // Valida o tipo de atendimento
        if ($senha->getTipoAtendimentoId() <= 0) {
            throw new InvalidArgumentException(
                'Selecione um tipo de atendimento.'
            );
        }

        return $this->repository->emitirSenha(
            $senha
        );
    }


    // Chama a próxima senha compatível com o balcão
    public function chamarProxima(
        int $balcaoId
    ): ?Senha {

        // Valida o balcão
        if ($balcaoId <= 0) {
            throw new InvalidArgumentException(
                'Informe um balcão válido.'
            );
        }


        // Busca o balcão
        $balcao =
            $this->balcaoRepository->buscarPorId(
                $balcaoId
            );


        // Verifica se o balcão existe
        if ($balcao === null) {
            throw new InvalidArgumentException(
                'Balcão não encontrado.'
            );
        }


        // Obtém o tipo de atendimento do balcão
        $tipoAtendimentoId =
            $balcao->getTipoAtendimentoId();


        // Valida o tipo de atendimento
        if ($tipoAtendimentoId <= 0) {
            throw new InvalidArgumentException(
                'O balcão não possui um tipo de atendimento válido.'
            );
        }


        // Solicita ao Repository a próxima senha
        // compatível com o tipo de atendimento
        $senha =
            $this->repository->chamarProxima(
                $balcaoId,
                $tipoAtendimentoId
            );


        // Nenhuma senha compatível encontrada
        if ($senha === null) {
            throw new InvalidArgumentException(
                'Não existem senhas aguardando atendimento para este balcão.'
            );
        }


        return $senha;
    }


// Inicia o atendimento de uma senha
public function iniciarAtendimento(
    string $codigo,
    int $balcaoId
): bool {

    // Valida o código
    if (trim($codigo) === '') {
        throw new InvalidArgumentException(
            'Informe o código da senha.'
        );
    }

    // Valida o balcão
    if ($balcaoId <= 0) {
        throw new InvalidArgumentException(
            'Informe um balcão válido.'
        );
    }

    // Busca a senha
    $senha = $this->repository->consultarPorCodigo(
        $codigo
    );

    // Verifica se existe
    if ($senha === null) {
        throw new InvalidArgumentException(
            'Senha não encontrada.'
        );
    }

    // Verifica se a senha pertence ao balcão
    if ($senha->getBalcaoId() !== $balcaoId) {
        throw new InvalidArgumentException(
            'A senha não pertence ao balcão selecionado.'
        );
    }

    // Verifica o estado atual
    if ($senha->getStatus() !== 'chamada') {
        throw new InvalidArgumentException(
            'A senha precisa estar no estado "chamada" para iniciar o atendimento.'
        );
    }

    // Persiste a alteração
    $sucesso = $this->repository->iniciarAtendimento(
        $codigo,
        $balcaoId
    );

    // Impede falso sucesso
    if (!$sucesso) {
        throw new InvalidArgumentException(
            'Não foi possível iniciar o atendimento.'
        );
    }

    return true;
}


// Finaliza o atendimento de uma senha
public function finalizarAtendimento(
    string $codigo,
    int $balcaoId,
    ?string $nomeCliente,
    ?string $telefoneContacto
): bool {

    // Valida o código
    if (trim($codigo) === '') {
        throw new InvalidArgumentException(
            'Informe o código da senha.'
        );
    }

    // Valida o balcão
    if ($balcaoId <= 0) {
        throw new InvalidArgumentException(
            'Informe um balcão válido.'
        );
    }

    // Busca a senha
    $senha = $this->repository->consultarPorCodigo(
        $codigo
    );

    // Verifica se existe
    if ($senha === null) {
        throw new InvalidArgumentException(
            'Senha não encontrada.'
        );
    }

    // Verifica se pertence ao balcão
    if ($senha->getBalcaoId() !== $balcaoId) {
        throw new InvalidArgumentException(
            'A senha não pertence ao balcão selecionado.'
        );
    }

    // Verifica o estado atual
    if ($senha->getStatus() !== 'em_atendimento') {
        throw new InvalidArgumentException(
            'A senha precisa estar em atendimento para ser finalizada.'
        );
    }

    // Persiste os dados
    $sucesso = $this->repository->finalizarAtendimento(
        $codigo,
        $balcaoId,
        $nomeCliente,
        $telefoneContacto
    );

    // Impede falso sucesso
    if (!$sucesso) {
        throw new InvalidArgumentException(
            'Não foi possível finalizar o atendimento.'
        );
    }

    return true;
}
}