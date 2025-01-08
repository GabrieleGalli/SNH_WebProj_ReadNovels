<?php

require_once 'crud.php';
require_once 'user.php';
require_once 'token.php';

const ENV = __DIR__ . '/../incl/.env';
// Load environment variables
$ENV = parse_ini_file(ENV);

$host = $ENV['host'];
$db_name = $ENV['db_name'];
$db_user = $ENV['db_user'];
$db_pass = $ENV['db_pass'];
$charset = $ENV['charset'];
// Data Source Name
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

try {
   // PDO instance
   $pdo = new PDO($dsn, $db_user, $db_pass);
   // Set the PDO error mode to exception
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   throw new PDOException($e->getMessage(), $e->getCode(), $e);
}

$Crud = new Crud($pdo);
$User = new User($pdo);
$Token = new Token($pdo);

?>