

<?php
// Documentação PHP: https://www.php.net/manual/pt_BR/book.pdo.php
//                   https://www.php.net/manual/pt_BR/ref.pdo-mysql.connection.php

$username = 'root';
$password = '';
$host = 'localhost';
$dbname = 'novo_banco_chris';

try{
    $db = new \PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8mb4', $username, $password, array(
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION

));
}catch(\PDOException $e){
    throw new \PDOException($e->getMessage());
}