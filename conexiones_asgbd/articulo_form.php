<?php
require 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errores = [];
$nombre = '';
$stock = '';
$rutaImagen = 'uploads/default.png';
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM articulos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $art = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$art) {
        echo "Artículo no encontrado.";
        exit;
    }
    $nombre = $art['nombre'];
    $stock = $art['stock'];
    $rutaImagen = $art['imagen'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';
    if ($nombre === '') $errores[] = "El nombre es obligatorio.";
    if ($stock === '' || !is_numeric($stock) || (int)$stock < 0) $errores[] = "El stock debe ser un número entero >= 0.";
    $imagenSubida = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['imagen']['tmp_name'];
            $origName = basename($_FILES['imagen']['name']);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (!in_array($ext, $allowed)) {
                $errores[] = "Formato de imagen no permitido. Usa jpg, png o gif.";
            } else {
                $newName = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
                if (!move_uploaded_file($tmp, $newName)) {
                    $errores[] = "Error al mover la imagen subida.";
                } else {
                    $imagenSubida = $newName;
                }
            }
        } else {
            $errores[] = "Error en la subida de la imagen.";
        }
    } else {
        if ($id === 0) {
            $errores[] = "La imagen es obligatoria.";
        }
    }
    if (empty($errores)) {
        if ($id > 0) {
            $imgGuardar = $imagenSubida ? $imagenSubida : $rutaImagen;
            $stmt = $pdo->prepare("UPDATE articulos SET nombre = :nombre, imagen = :imagen, stock = :stock WHERE id = :id");
            $stmt->execute([
                ':nombre' => $nombre,
                ':imagen' => $imgGuardar,
                ':stock' => (int)$stock,
                ':id' => $id
            ]);
        } else {
            $imgGuardar = $imagenSubida ? $imagenSubida : 'uploads/default.png';
            $stmt = $pdo->prepare("INSERT INTO articulos (nombre, imagen, stock) VALUES (:nombre, :imagen, :stock)");
            $stmt->execute([
                ':nombre' => $nombre,
                ':imagen' => $imgGuardar,
                ':stock' => (int)$stock
            ]);
        }
        header('Location: index.php');
        exit;
    } else {
        if (isset($imagenSubida) && file_exists($imagenSubida)) {
            @unlink($imagenSubida);
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?php echo $id>0 ? 'Editar' : 'Añadir'; ?> artículo</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header class="header">
    <h1><?php echo $id>0 ? 'Editar artículo' : 'Añadir artículo'; ?></h1>
    <a class="btn" href="index.php">Volver</a>
  </header>
  <main class="container form-container">
    <?php if (!empty($errores)): ?>
      <div class="errors">
        <ul>
          <?php foreach ($errores as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="form">
      <label>Nombre
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
      </label>
      <label>Stock
        <input type="number" name="stock" min="0" value="<?php echo htmlspecialchars($stock); ?>" required>
      </label>
      <label>Imagen (subir)
        <input type="file" name="imagen" accept="image/*" <?php echo $id===0 ? 'required' : ''; ?>>
      </label>
      <?php if ($id>0): ?>
        <p>Imagen actual:</p>
        <div class="img-preview">
          <img src="<?php echo htmlspecialchars($rutaImagen); ?>" alt="imagen actual">
        </div>
      <?php endif; ?>
      <div class="form-actions">
        <button class="btn" type="submit"><?php echo $id>0 ? 'Actualizar' : 'Insertar'; ?></button>
        <a class="btn secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </main>
  <footer class="footer">
    <p>Práctica 2ºASIR - Inventario</p>
  </footer>
</body>
</html>
