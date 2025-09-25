<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;

class TesteModel {
    public function __construct() {
        $db = Database::getInstance();
        echo "✅ Conexão estabelecida com sucesso.\n";
    }
}

new TesteModel();
