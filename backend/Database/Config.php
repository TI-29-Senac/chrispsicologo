<?php
namespace App\Psico\Database;

use Dotenv\Dotenv;

class Config {
    public static function get()
    {
<<<<<<< HEAD
        // Carrega variáveis de ambiente (mantido por compatibilidade)
        self::loadEnv();
 
        return [
            'database' => [
                'driver' => 'mysql',
                'mysql' => [
                    // --- CONFIGURAÇÃO DO BANCO DE DADOS (REMOTO) ---
                    'host' => '216.172.172.207',  // IP do servidor
                    'db_name' => 'faust537_time1_ti29', // Nome do banco
                    'username' => 'faust537_time1_ti29', // Usuário
                    'password' => 'Mb==pK.sh,)4',        // Senha
                    'port' => 3306,
                    'charset' => 'utf8mb4',
                ]
            ],
            'mailer' => [
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'username' => 'dollyblair18@gmail.com',
                'password' => 'syty qeco pslp elir', // App Password do Gmail
                'encryption' => 'tls',
                'from_address' => 'dollyblair18@gmail.com',
                'from_name' => 'Chris Psicologia',
            ]
        ];
    }
 
    private static function loadEnv()
    {
        $dotenvPath = __DIR__ . '/../../.env';
        if (is_readable($dotenvPath)) {
            $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                        putenv(sprintf('%s=%s', $name, $value));
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
=======
        // Garante que as variáveis de ambiente sejam carregadas caso ainda não tenham sido
        // Idealmente isso deveria ser feito no bootstrap da aplicação (index.php),
        // mas mantemos aqui para compatibilidade com o código existente.
        try {
            // Caminho para a raiz do projeto (onde está o .env e o vendor)
            // __DIR__ = backend/Database
            // ../../ = raiz
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();
        } catch (\Exception $e) {
            // Silencia erro se não achar .env em produção (assume variáveis de sistema)
>>>>>>> 377194288474c82d3166ffcc4e6d1d99ef489b85
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