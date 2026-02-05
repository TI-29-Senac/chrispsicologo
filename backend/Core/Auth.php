<?php
namespace App\Psico\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Psico\Core\Response;

class Auth {
    private static $algorithm = 'HS256';

    /**
     * Retorna a chave secreta do .env
     */
    private static function getSecretKey() {
        // A chave deve ter pelo menos 32 caracteres (256 bits) para HS256
        return $_ENV['JWT_SECRET'] ?? $_ENV['API_TOKEN'] ?? 'default_unsafe_secret_change_me_to_be_32_chars_long';
    }

    /**
     * Gera um token JWT para o usuário
     */
    public static function generate(int $userId, string $role) {
        $issuedAt = time();
        $expirationTime = $issuedAt + (60 * 60 * 8); // Válido por 8 horas
        
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, self::getSecretKey(), self::$algorithm);
    }

    /**
     * Valida um token e retorna o payload decodificado (objeto) ou null se inválido
     */
    public static function validate($token) {
        try {
            return JWT::decode($token, new Key(self::getSecretKey(), self::$algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Middleware: Verifica o header Authorization e para a execução se inválido.
     * Pode ser chamado no início de rotas protegidas.
     */
    public static function check() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error('Token não fornecido ou inválido.', 401);
            exit; // Garante parada
        }

        $token = $matches[1];

        // 1. Tenta validar como Token Fixo (API_TOKEN do .env)
        // Isso permite que o Desktop App acesse sem login inicial para sincronizar/logar
        $apiToken = $_ENV['API_TOKEN'] ?? '';
        if (!empty($apiToken) && $token === $apiToken) {
            // Retorna um payload "Mestre" fictício para permitir acesso
            return (object) [
                'sub' => 0, // ID 0 ou outro identificador de sistema
                'role' => 'admin', // Permissão total
                'iat' => time(),
                'exp' => time() + 3600 // Válido por 1h (embora não validado por tempo aqui)
            ];
        }

        // 2. Se não for Token Fixo, tenta validar como JWT
        $payload = self::validate($token);

        if (!$payload) {
            Response::error('Token expirado ou inválido.', 401);
            exit;
        }

        // Opcional: Retorna o payload para uso no controller
        return $payload;
    }
}
