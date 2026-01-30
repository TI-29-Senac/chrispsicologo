<?php
// backend/test_db_connection.php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

header('Content-Type: text/plain');

echo "Iniciando teste de conexão...\n";

try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // Adjust path to root
    $dotenv->safeLoad();
    echo "Dotenv carregado com sucesso.\n";
} catch (\Exception $e) {
    echo "Erro ao carregar Dotenv: " . $e->getMessage() . "\n";
}

$host = $_ENV['DB_HOST'] ?? 'UNDEFINED';
$db   = $_ENV['DB_NAME'] ?? 'UNDEFINED';
$user = $_ENV['DB_USER'] ?? 'UNDEFINED';
$pass = $_ENV['DB_PASS'] ?? 'UNDEFINED';
$port = $_ENV['DB_PORT'] ?? 3306;

echo "Configurações (Senha ocultada):\n";
echo "Host: $host\n";
echo "Database: $db\n";
echo "User: $user\n";
echo "Port: $port\n";

if ($host === 'UNDEFINED') {
    die("ERROCRÍTICO: Variáveis de ambiente não encontradas. Verifique o arquivo .env ou as variáveis do sistema.\n");
}

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4;port=$port";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "\nSUCESSO: Conexão com o banco de dados estabelecida!\n";
} catch (PDOException $e) {
    echo "\nERRO DE CONEXÃO: " . $e->getMessage() . "\n";
}
