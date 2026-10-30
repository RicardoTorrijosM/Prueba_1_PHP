<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$directorio = "comprobantes/";
$archivos = [];

if (is_dir($directorio)) {
    $archivos = array_diff(scandir($directorio), ['.', '..']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Archivos Cargados</title>
    <style>
        .contenedor {
            max-width: 600px; margin: 30px auto; padding: 24px;
            background: #f5f5f5; border-radius: 16px;
            font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10);
        }
        ul { background: #fff; padding: 20px 35px; border-radius: 8px; }
        li { margin-bottom: 8px; }
        .volver { display: inline-block; margin-top: 15px; text-decoration: none; color: #1976d2; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Comprobantes Cargados</h2>
        <?php if (empty($archivos)): ?>
            <p>No hay archivos cargados actualmente.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($archivos as $archivo): ?>
                    <li>
                        <a href="<?php echo $directorio . rawurlencode($archivo); ?>" target="_blank">
                            <?php echo htmlspecialchars($archivo); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>