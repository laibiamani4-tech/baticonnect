<?php
$host = 'localhost';
$dbname = 'baticonnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    session_start();
} catch(PDOException $e) {
    die(json_encode(['error' => 'Connexion DB: ' . $e->getMessage()]));
}
?>