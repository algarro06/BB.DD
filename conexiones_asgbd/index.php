<?php
require 'db.php';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$orden = isset($_GET['orden']) && $_GET['orden'] === 'desc' ? 'DESC' : 'ASC';
$params = [];
$sql = "SELECT id, nombre, imagen, stock FROM articulos";
if ($busqueda !== '') {
    $sql .= " WHERE lower(unaccent(nombre)) LIKE lower(unaccent(:busqueda))";
    $params[':busqueda'] = '%' . $busqueda . '%';
}
$sql .= " ORDER BY nombre $orden";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Inventario</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header class="header">
    <h1>Inventario del Almacén</h1>
    <a class="btn add" href="articulo_form.php">+ Añadir artículo</a>
  </header>
  <main class="container">
    <form method="get" class="search-form" novalidate>
      <input type="text" name="busqueda" placeholder="Buscar por nombre..." value="<?php echo htmlspecialchars($busqueda); ?>" required>
      <button type="submit" class="btn">Buscar</button>
      <div class="order-links">
        <span>Orden:</span>
        <?php
          $qsAsc = '?orden=asc' . ($busqueda !== '' ? '&busqueda=' . urlencode($busqueda) : '');
          $qsDesc = '?orden=desc' . ($busqueda !== '' ? '&busqueda=' . urlencode($busqueda) : '');
        ?>
        <a class="link" href="<?php echo $qsAsc; ?>">A → Z</a> |
        <a class="link" href="<?php echo $qsDesc; ?>">Z → A</a>
      </div>
    </form>
    <section class="grid">
      <?php if (count($articulos) === 0): ?>
        <p class="empty">No hay artículos que coincidan.</p>
      <?php else: ?>
        <?php foreach ($articulos as $a): ?>
          <article class="card">
            <div class="img-wrap">
              <img src="<?php echo htmlspecialchars($a['imagen']); ?>" alt="<?php echo htmlspecialchars($a['nombre']); ?>">
            </div>
            <h3><?php echo htmlspecialchars($a['nombre']); ?></h3>
            <p class="stock">Stock: <strong><?php echo (int)$a['stock']; ?></strong></p>
            <div class="actions">
              <a class="btn small" href="articulo_form.php?id=<?php echo (int)$a['id']; ?>">Editar</a>
              <a class="btn danger small" href="confirmar_borrado.php?id=<?php echo (int)$a['id']; ?>">Borrar</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
  <footer class="footer">
    <p>Práctica 2ºASIR - Inventario</p>
  </footer>
</body>
</html>
