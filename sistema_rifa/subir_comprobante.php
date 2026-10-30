<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprobante'])) {
    $directorio = "comprobantes/";

    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombre_archivo = basename($_FILES['comprobante']['name']);
    $ruta_destino = $directorio . $nombre_archivo;

    if ($_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_destino)) {
            $mensaje = "El archivo '$nombre_archivo' fue cargado correctamente.";
        } else {
            $mensaje = "Error al guardar el archivo en el servidor.";
        }
    } else {
        $mensaje = "Ocurrió un error durante la carga del archivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Comprobante</title>
    <style>
        .contenedor {
            max-width: 500px; margin: 30px auto; padding: 24px;
            background: #f5f5f5; border-radius: 16px;
            font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10);
        }
        .btn { padding: 10px 18px; background: #1976d2; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .volver { display: inline-block; margin-top: 15px; text-decoration: none; color: #1976d2; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Subir Comprobante de Pago</h2>
        <form action="subir_comprobante.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="comprobante" required /><br>
            <input type="submit" class="btn" value="Subir Archivo" />
        </form>

        <?php if ($mensaje !== ""): ?>
            <p><strong><?php echo htmlspecialchars($mensaje); ?></strong></p>
        <?php endif; ?>

        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>