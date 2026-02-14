<?php
$host = '127.0.0.1';
$port = '5432';
$dbname = 'inventario';
$user = 'inventario_user';
$pass = '123456';
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo "Error conexión BD: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

