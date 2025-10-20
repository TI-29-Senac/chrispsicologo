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
                ],
            ]
        ];
    }

    /**
     * Carrega as variáveis de ambiente de um arquivo .env na raiz do projeto.
     */
    private static function loadEnv()
    {
        // --- CORREÇÃO APLICADA AQUI ---
        // O caminho correto é subir dois níveis a partir de /backend/Database/
        $dotenvPath = __DIR__ . '/../../.env'; 

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