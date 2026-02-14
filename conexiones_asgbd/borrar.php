<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}
$stmt = $pdo->prepare("SELECT imagen FROM articulos WHERE id = :id");
$stmt->execute([':id' => $id]);
$art = $stmt->fetch(PDO::FETCH_ASSOC);
if ($art) {
    $img = $art['imagen'];
    $del = $pdo->prepare("DELETE FROM articulos WHERE id = :id");
    $del->execute([':id' => $id]);
    if ($img && file_exists($img) && strpos($img, 'uploads/default') === false) {
        @unlink($img);
    }
}
header('Location: index.php');
exit;
?>
