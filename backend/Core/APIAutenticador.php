<?php

namespace App\Psico\Core;

class APIAutenticador {
    // Idealmente, isto viria do .env ou Config.php, mas para centralizar agora:
    private static $chaveAPI = "73C60B2A5B23B2300B235AF6EE616F46167F2B830E78F0A8DDCBDF5C9598BCAD";

    /**
     * Verifica se a requisição atual possui um Token Bearer válido.
     * * @return bool Retorna true se autenticado, false caso contrário.
     */
    public static function validar(): bool {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = null;

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader) {
            return false;
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            $token = $authHeader;
        }

        return $token === self::$chaveAPI;
    }

    public static function enviarErroNaoAutorizado() {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Acesso negado: Chave de API inválida ou ausente.'
        ]);
        exit;
    }
}