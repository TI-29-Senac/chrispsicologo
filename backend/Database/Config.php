<?php
namespace App\Psico\Database;
 
class Config {
    public static function get()
    {
        // Carrega as variáveis de ambiente do arquivo .env
        self::loadEnv();
 
        return [
    'database' => [
        'driver' => 'mysql',
        'mysql' => [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'db_name' => getenv('DB_NAME') ?: '',
            'username' => getenv('DB_USER') ?: '',
            'password' => getenv('DB_PASS') ?: '',
            'charset' => 'utf8mb4',
            'port' => getenv('DB_PORT') ?: 3306
        ]
    ],
    'mailer' => [
        'host' => getenv('MAIL_HOST') ?: 'localhost',
        'port' => (int)(getenv('MAIL_PORT') ?: 587),
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: '',
        'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'nao-responda@localhost',
        'from_name' => trim(getenv('MAIL_FROM_NAME') ?: 'Chris Psicologia'),
    ]
];
 
    }
 
    /**
     * Carrega as variáveis de ambiente de um arquivo .env na raiz do projeto.
     */
    private static function loadEnv()
    {
        $dotenvPath = __DIR__ . '/../.env';
 
        if (is_readable($dotenvPath)) {
            $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
 
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
        }
    }
}