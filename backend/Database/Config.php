<?php
namespace App\Psico\Database;

use Dotenv\Dotenv;

class Config {
    public static function get()
    {
        // Garante que as variáveis de ambiente sejam carregadas caso ainda não tenham sido
        try {
            // Caminho para a raiz do projeto (onde está o .env e o vendor)
            // __DIR__ = backend/Database
            // ../../ = raiz
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();
        } catch (\Exception $e) {
            // Silencia erro se não achar .env em produção (assume variáveis de sistema)
        }

        // Helper: lê de $_ENV primeiro, depois getenv() (compatível com Vercel e outros serverless)
        $env = function(string $key, $default = '') {
            return $_ENV[$key] ?? getenv($key) ?: $default;
        };

        return [
            'database' => [
                'driver' => 'mysql',
                'mysql' => [
                    'host'     => $env('DB_HOST', 'localhost'),
                    'db_name'  => $env('DB_NAME'),
                    'username' => $env('DB_USER'),
                    'password' => $env('DB_PASS'),
                    'charset'  => 'utf8mb4',
                    'port'     => $env('DB_PORT', 3306)
                ]
            ],
            'mailer' => [
                'host'         => $env('MAIL_HOST', 'localhost'),
                'port'         => (int)$env('MAIL_PORT', 587),
                'username'     => $env('MAIL_USERNAME'),
                'password'     => $env('MAIL_PASSWORD'),
                'encryption'   => $env('MAIL_ENCRYPTION'),
                'from_address' => $env('MAIL_FROM_ADDRESS', 'nao-responda@localhost'),
                'from_name'    => trim($env('MAIL_FROM_NAME', 'Chris Psicologia')),
            ]
        ];
    }
}