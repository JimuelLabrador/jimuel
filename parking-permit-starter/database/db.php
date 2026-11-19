<?php

$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "campus-security";
$port = 3306;
$charset = "utf8mb4";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";
    $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to database server.";
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}