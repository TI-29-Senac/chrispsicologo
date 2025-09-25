<?php
namespace App\Database;

use PDO;
use PDOException;
use Exception;
use App\Database\Config;

class Database {
    private static $instance = null;
    private $conn;
    private $config;

    private function __construct() {
        $this->config = Config::get();
        $dbConfig = $this->config['database'];
        $driver = $dbConfig['driver'];

        try {
            switch ($driver) {
                case 'mysql':
                    $mysqlConfig = $dbConfig['mysql'];
                    $dsn = "mysql:host={$mysqlConfig['host']};dbname={$mysqlConfig['db_name']};charset={$mysqlConfig['charset']}";
                    if (!empty($mysqlConfig['port'])) {
                        $dsn .= ";port={$mysqlConfig['port']}";
                    }

                    $this->conn = new PDO(
                        $dsn,
                        $mysqlConfig['username'],
                        $mysqlConfig['password'],
                        [PDO::ATTR_PERSISTENT => true]
                    );
                    break;
            }

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $exception) {
            echo "Erro de conexão (PDO): " . $exception->getMessage();
        } catch(Exception $exception) {
            echo "Erro de conexão (Geral): " . $exception->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }

    public static function destroyInstance() {
        self::$instance = null;
    }
}
