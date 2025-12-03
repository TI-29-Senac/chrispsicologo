<?php

namespace App\Psico\Core;

use App\Psico\Database\Config;

class APIAutenticador {

    public static function validar(): bool {
        Config::get(); 
        
        $chaveEsperada = getenv('API_TOKEN');

        if (!$chaveEsperada) {
            error_log("ERRO DE SEGURANÇA: API_TOKEN não configurado no arquivo .env");
            return false;
        }

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

        return $token === $chaveEsperada;
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