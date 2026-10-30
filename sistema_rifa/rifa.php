<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$precio_unitario = 2000;

if (!isset($_SESSION['cantidad_numeros'])) {
    $_SESSION['cantidad_numeros'] = 0;
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    $accion = $_POST['accion'] ?? '';

    if ($cantidad > 0) {
        if ($accion === 'agregar') {
            $_SESSION['cantidad_numeros'] += $cantidad;
            $mensaje = "Se agregaron $cantidad número(s) correctamente.";
        } elseif ($accion === 'quitar') {
            if ($_SESSION['cantidad_numeros'] - $cantidad < 0) {
                $_SESSION['cantidad_numeros'] = 0;
                $mensaje = "No se permiten cantidades negativas. Reserva ajustada a 0.";
            } else {
                $_SESSION['cantidad_numeros'] -= $cantidad;
                $mensaje = "Se quitaron $cantidad número(s) correctamente.";
            }
        }
    } else {
        $mensaje = "Ingrese una cantidad mayor a 0.";
    }
}

$cantidad_reservada = $_SESSION['cantidad_numeros'];
$total_pagar = $cantidad_reservada * $precio_unitario;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Números</title>
    <style>
        .contenedor {
            max-width: 600px; margin: 30px auto; padding: 24px;
            background: #f5f5f5; border-radius: 16px;
            font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10);
        }
        .resumen { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .btn { padding: 10px 18px; border: none; border-radius: 6px; color: white; font-weight: bold; cursor: pointer; }
        .btn-agregar { background: #388e3c; }
        .btn-quitar { background: #d32f2f; }
        .volver { display: inline-block; margin-top: 15px; text-decoration: none; color: #1976d2; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Reserva de Números de Rifa</h2>

        <div class="resumen">
            <p><strong>Cantidad de números reservados:</strong> <?php echo $cantidad_reservada; ?></p>
            <p><strong>Precio de cada número:</strong> $<?php echo number_format($precio_unitario, 0, ',', '.'); ?></p>
            <p><strong>Total a pagar:</strong> $<?php echo number_format($total_pagar, 0, ',', '.'); ?></p>
        </div>

        <form action="rifa.php" method="POST">
            <label><strong>Cantidad de números:</strong></label>
            <input type="number" name="cantidad" value="1" min="1" required style="padding: 8px; width: 80px;" />
            <button type="submit" name="accion" value="agregar" class="btn btn-agregar">Agregar Números</button>
            <button type="submit" name="accion" value="quitar" class="btn btn-quitar">Quitar Números</button>
        </form>

        <?php if ($mensaje !== ""): ?>
            <p><b><?php echo htmlspecialchars($mensaje); ?></b></p>
        <?php endif; ?>

        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>