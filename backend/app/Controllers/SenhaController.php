<?php

namespace App\Controllers;

use App\Requests\Request;
use App\Models\Senha;
use App\Responses\JsonResponse;
use App\Services\SenhaService;
use App\Exceptions\DatabaseException;
use InvalidArgumentException;


// Responsável por receber as requisições relacionadas às Senhas
class SenhaController
{
    private SenhaService $service;

    // Recebe o Service por injeção de dependência
    public function __construct(SenhaService $service)
    {
        $this->service = $service;
    }

    // Retorna as senhas com limite de 10 por pagina
    public function listar(): void
    {
        try {

            $senhas = $this->service->listar();

            JsonResponse::success(
                'Senhas listadas com sucesso.',
                $senhas
            );
        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao listar as senhas.',
                500
            );
        }
    }

    // Consulta uma senha pelo código
    public function consultar(string $codigo): void
    {
        try {

            $senha = $this->service->consultarPorCodigo($codigo);

            if ($senha === null) {

                JsonResponse::error(
                    'Senha não encontrada.',
                    404
                );

                return;
            }

            JsonResponse::success(
                'Senha consultada com sucesso.',
                $senha
            );
        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao consultar a senha.',
                500
            );
        }
    }

    // Emite uma nova senha
    public function emitir(Request $request): void
    {
        try {

            $senha = new Senha(
                null,
                '',
                $request->input('nomeCliente'),
                $request->input('telefoneContacto'),
                $request->input('tipoAtendimentoId'),
                null,
                '',
                '',
                null,
                null,
                null
            );

            $resultado = $this->service->emitirSenha($senha);

            JsonResponse::success(
                'Senha emitida com sucesso.',
                $resultado,
                201
            );
        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao emitir a senha.',
                500
            );
        }
    }

    // Chama a próxima senha compatível com o balcão do utilizador
    public function chamarProxima(Request $request): void
    {
        try {

            // Obtém os dados do utilizador autenticado
            $utilizador =
                $request->utilizadorAutenticado();


            // Obtém o ID do balcão através do JWT
            $balcaoId =
                isset($utilizador['balcaoId'])
                ? (int) $utilizador['balcaoId']
                : 0;


            // Verifica se o utilizador possui um balcão
            if ($balcaoId <= 0) {

                JsonResponse::error(
                    'Nenhum balcão está associado ao utilizador.',
                    400
                );

                return;
            }


            // Solicita ao Service a próxima senha
            $senha =
                $this->service->chamarProxima(
                    $balcaoId
                );


            JsonResponse::success(
                'Senha chamada com sucesso.',
                $senha
            );
        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao chamar a próxima senha.',
                500
            );
        }
    }

    // Inicia o atendimento de uma senha
    public function iniciarAtendimento(
        Request $request
    ): void {

        try {

            // Obtém o código enviado pelo Angular
            $codigo = $request->input('codigo');

            if (!is_string($codigo) || trim($codigo) === '') {
                JsonResponse::error(
                    'Informe o código da senha.',
                    400
                );

                return;
            }

            // Obtém o utilizador autenticado através do JWT
            $utilizador =
                $request->utilizadorAutenticado();

            // Obtém o balcão associado ao utilizador
            $balcaoId = isset($utilizador['balcaoId'])
                ? (int) $utilizador['balcaoId']
                : 0;

            // Verifica se existe balcão associado
            if ($balcaoId <= 0) {

                JsonResponse::error(
                    'Nenhum balcão está associado ao utilizador.',
                    400
                );

                return;
            }

            // Envia apenas os dados necessários para o Service
            $sucesso =
                $this->service->iniciarAtendimento(
                    (string) $codigo,
                    $balcaoId
                );

            JsonResponse::success(
                'Atendimento iniciado com sucesso.',
                $sucesso
            );
        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao iniciar o atendimento.',
                500
            );
        }
    }


    // Finaliza o atendimento de uma senha
    public function finalizarAtendimento(
        Request $request
    ): void {

        try {

            // Obtém os dados enviados pelo Angular
            $codigo = $request->input('codigo');



            if (!is_string($codigo) || trim($codigo) === '') {
                JsonResponse::error(
                    'Informe o código da senha.',
                    400
                );

                return;
            }

            $nomeCliente =
                $request->input('nomeCliente');

            $telefoneContacto =
                $request->input('telefoneContacto');

            // Obtém o utilizador autenticado
            $utilizador =
                $request->utilizadorAutenticado();

            // Obtém o balcão através do JWT
            $balcaoId = isset($utilizador['balcaoId'])
                ? (int) $utilizador['balcaoId']
                : 0;

            // Verifica se existe balcão
            if ($balcaoId <= 0) {

                JsonResponse::error(
                    'Nenhum balcão está associado ao utilizador.',
                    400
                );

                return;
            }

            // Envia os dados para o Service
            $sucesso =
                $this->service->finalizarAtendimento(
                    (string) $codigo,
                    $balcaoId,
                    $nomeCliente,
                    $telefoneContacto
                );

            JsonResponse::success(
                'Atendimento finalizado com sucesso.',
                $sucesso
            );
        } catch (InvalidArgumentException $exception) {

            JsonResponse::error(
                $exception->getMessage(),
                400
            );
        } catch (DatabaseException $exception) {

            JsonResponse::error(
                'Erro ao finalizar o atendimento.',
                500
            );
        }
    }
}
