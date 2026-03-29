<?php
$host = "mysql.itu.edu.tr";
$db   = "db97384";
$user = "db97384";
$pass = "4Kr8uAiKaw";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
session_start();
?>