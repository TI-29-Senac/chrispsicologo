<?php
namespace App\Psico\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Psico\Core\Response;
use App\Psico\Database\Database;
use PDO;

class Auth {
    private static $algorithm = 'HS256';

    /**
     * Retorna a chave secreta do .env
     */
    private static function getSecretKey() {
        if (!isset($_ENV['JWT_SECRET']) || empty($_ENV['JWT_SECRET'])) {
            throw new \Exception('FATAL: JWT_SECRET não configurada no arquivo .env');
        }
        return $_ENV['JWT_SECRET'];
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
        // Lê o header Authorization com fallbacks para ambientes serverless (ex: Vercel)
        $authHeader = '';

        // Prioridade 1: $_SERVER['HTTP_AUTHORIZATION'] (padrão CGI/FastCGI)
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        // Prioridade 2: $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] (quando há rewrite rules)
        elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        // Prioridade 3: getallheaders() (Apache mod_php)
        elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

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

    /**
     * Gera um Refresh Token (aleatório, opaco), salva no banco (hash) e retorna o token puro.
     * Validade: 30 dias.
     */
    public static function generateRefreshToken(int $userId) {
        $token = bin2hex(random_bytes(32)); // 64 chars
        $hash = hash('sha256', $token);
        
        $expiresAt = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30)); // 30 dias

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $hash, $expiresAt]);

        return $token;
    }

    /**
     * Verifica se o refresh token é válido e retorna o user_id.
     * Retorna false se inválido ou expirado.
     */
    public static function verifyRefreshToken($token) {
        $hash = hash('sha256', $token);
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT user_id, expires_at FROM refresh_tokens WHERE token_hash = ?");
        $stmt->execute([$hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return false;

        if (strtotime($row['expires_at']) < time()) {
            // Expirado
            self::revokeRefreshToken($token);
            return false;
        }

        return (int)$row['user_id'];
    }

    /**
     * Remove o refresh token do banco.
     */
    public static function revokeRefreshToken($token) {
        $hash = hash('sha256', $token);
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM refresh_tokens WHERE token_hash = ?");
        $stmt->execute([$hash]);
    }
}

