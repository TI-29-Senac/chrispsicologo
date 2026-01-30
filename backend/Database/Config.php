<?php
namespace App\Psico\Database;
 
class Config {
    public static function get()
    {
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
        }
    }
}