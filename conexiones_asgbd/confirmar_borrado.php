<?php
require 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM articulos WHERE id = :id");
$stmt->execute([':id' => $id]);
$art = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$art) {
    echo "Artículo no encontrado.";
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Confirmar borrado</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header class="header">
    <h1>Confirmar borrado</h1>
  </header>
  <main class="container">
    <div class="confirm-box">
      <p>¿Seguro que quieres borrar el artículo <strong><?php echo htmlspecialchars($art['nombre']); ?></strong>?</p>
      <div class="img-preview small">
        <img src="<?php echo htmlspecialchars($art['imagen']); ?>" alt="">
      </div>
      <form method="post" action="borrar.php">
        <input type="hidden" name="id" value="<?php echo (int)$art['id']; ?>">
        <button class="btn danger" type="submit">Sí, borrar</button>
        <a class="btn" href="index.php">No, volver</a>
      </form>
    </div>
  </main>
  <footer class="footer">
    <p>Inventario</p>
  </footer>
</body>
</html>
