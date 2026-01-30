<?php
namespace App\Psico\Core;

class Response {
    /**
     * Retorna uma resposta JSON genérica e para a execução.
     */
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Retorna uma resposta de sucesso padrão: { "success": true, ...$data }
     */
    public static function success($data = [], $status = 200) {
        $payload = ['success' => true];
        if (is_array($data)) {
            $payload = array_merge($payload, $data);
        }
        self::json($payload, $status);
    }

    /**
     * Retorna uma resposta de erro padrão: { "success": false, "error": "msg" }
     */
    public static function error($message, $status = 400, $data = []) {
        $payload = ['success' => false, 'error' => $message];
        if (is_array($data)) {
            $payload = array_merge($payload, $data);
        }
        self::json($payload, $status);
    }
}
