<?php
// config/bd.php
/*
// Local
$server = 'localhost';
$dbname = 'foot';
$username = 'user';
$password = '';

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$dbname", "$username", "$password");
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection error: ' . $e->getMessage());
}


*/
// alwaysdata
$server = 'mysql-mongestionfoot.alwaysdata.net';
$dbname = 'mongestionfoot_user';
$username = '394736';
$password = 'pipi1234?';

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$dbname", "$username", "$password");
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection error: ' . $e->getMessage());
}

?>