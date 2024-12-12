<?php

require_once 'crud.php';
require_once 'user.php';
require_once 'token.php';


$host = 'localhost';
$db_name = 'snh_webproj_novels';
$db_user = 'root';
$db_pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

try {
   $pdo = new PDO($dsn, $db_user, $db_pass);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   throw new PDOException($e->getMessage(), $e->getCode(), $e);
}

$Crud = new Crud($pdo);
$User = new User($pdo);
$Token = new Token($pdo);

?>