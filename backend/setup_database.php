<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Psico\Database\Database;

// Carregar .env manualmente para garantir
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $db = Database::getInstance();

    $sql = "
    CREATE TABLE IF NOT EXISTS refresh_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token_hash VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (token_hash),
        FOREIGN KEY (user_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "Tabela 'refresh_tokens' criada ou já existente com sucesso.\n";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    if ($e->getPrevious()) {
        echo "Detalhes: " . $e->getPrevious()->getMessage() . "\n";
    }
    exit(1);
}
