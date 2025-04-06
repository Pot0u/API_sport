<?php

// alwaysdata
$server = 'mysql-mongestionapiauth.alwaysdata.net';
$dbname = 'mongestionapiauth_user';
$username = '407922';
$password = 'pipi1234?';

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$dbname", "$username", "$password");
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection error: ' . $e->getMessage());
}

?>