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

        return [
            'database' => [
                'driver' => 'mysql',
                'mysql' => [
                    'host' => $_ENV['DB_HOST'] ?? 'localhost',
                    'db_name' => $_ENV['DB_NAME'] ?? '',
                    'username' => $_ENV['DB_USER'] ?? '',
                    'password' => $_ENV['DB_PASS'] ?? '',
                    'charset' => 'utf8mb4',
                    'port' => $_ENV['DB_PORT'] ?? 3306
                ]
            ],
            'mailer' => [
                'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
                'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
                'username' => $_ENV['MAIL_USERNAME'] ?? '',
                'password' => $_ENV['MAIL_PASSWORD'] ?? '',
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? '',
                'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'nao-responda@localhost',
                'from_name' => trim($_ENV['MAIL_FROM_NAME'] ?? 'Chris Psicologia'),
            ]
        ];
    }
}