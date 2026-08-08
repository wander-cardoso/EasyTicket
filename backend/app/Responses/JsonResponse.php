<?php

namespace App\Responses;

//Classe responsável por padronizar todas as respostas da API no formato JSON
class JsonResponse
{
    // Retorna uma resposta de sucesso
    public static function success(
        string $message,
        mixed $data = [],
        int $statusCode = 200
    ): void {
        self::sendSuccess(
            $message,
            $data,
            $statusCode
        );
    }

    //Retorna uma resposta de erro
    public static function error(
        string $message,
        int $statusCode = 400
    ): void {

        self::sendError(
            $message,
            $statusCode
        );
    }

    //Este método é privado porque somente success() deve utilizá-lo
   private static function sendSuccess(
    string $message,
    mixed $data,
    int $statusCode
): void
{
    http_response_code($statusCode);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

    //Este método é privado porque somente error() deve utilizá-lo
private static function sendError(
    string $message,
    int $statusCode
): void
{
    http_response_code($statusCode);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => false,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
}