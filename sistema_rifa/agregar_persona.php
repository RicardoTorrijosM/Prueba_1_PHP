<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$ruta_json = '../api_rest/participantes.json';
$mensaje = "";
$error = "";
$resumen_pago = "";
$precio_unitario = 2000;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $numeros_input = trim($_POST['numeros'] ?? '');

    // Limpiar y separar los números ingresados
    $numeros_array = array_filter(array_map('trim', explode(',', $numeros_input)));

    if (!file_exists($ruta_json)) {
        file_put_contents($ruta_json, json_encode([]));
    }
    $participantes = json_decode(file_get_contents($ruta_json), true);

    // 1. Extraer todos los números que ya están comprados
    $numeros_ocupados = [];
    foreach ($participantes as $p) {
        $numeros_ocupados[] = (int)$p['numero_rifa'];
    }

    // 2. Revisar si alguno de los números ingresados ya está en la lista
    $numeros_duplicados = [];
    foreach ($numeros_array as $num) {
        if (in_array((int)$num, $numeros_ocupados)) {
            $numeros_duplicados[] = $num;
        }
    }

    // 3. Bloquear o Guardar
    if (count($numeros_duplicados) > 0) {
        $error = "ERROR: El/los número(s) " . implode(', ', $numeros_duplicados) . " ya está(n) utilizado(s).";
    } else {
        $cantidad_comprada = count($numeros_array);
        $total_pagar = $cantidad_comprada * $precio_unitario;

        foreach ($numeros_array as $num) {
            $nuevo_id = 1;
            foreach ($participantes as $p) {
                if ($p['id'] >= $nuevo_id) {
                    $nuevo_id = $p['id'] + 1;
                }
            }
            $participantes[] = [
                "id" => $nuevo_id,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "ciudad" => $ciudad,
                "categoria" => $categoria,
                "numero_rifa" => (int)$num
            ];
        }

        file_put_contents($ruta_json, json_encode($participantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $mensaje = "¡$nombre $apellido registrado exitosamente con $cantidad_comprada número(s)!";
        $resumen_pago = "Total a cobrar: $" . number_format($total_pagar, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inscribir y Vender</title>
    <style>
        .contenedor { max-width: 550px; margin: 30px auto; padding: 24px; background: #f5f5f5; border-radius: 16px; font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10); }
        .fila-campos { display: flex; gap: 15px; } /* Para poner campos uno al lado del otro */
        .campo { margin-bottom: 15px; flex: 1; }
        .campo label { font-weight: bold; display: block; margin-bottom: 5px; }
        .campo input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 6px; }
        .btn { padding: 12px 20px; background: #4caf50; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; margin-top: 10px;}
        .btn:hover { background: #388e3c; }
        .volver { display: inline-block; margin-top: 20px; text-decoration: none; color: #1976d2; font-weight: bold; }
        .alerta-exito { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 6px; border: 1px solid #c8e6c9; margin-bottom: 15px; text-align: center; }
        .alerta-error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 15px; text-align: center; font-weight: bold;}
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>👤 Vender Números</h2>
        
        <?php if ($error !== ""): ?>
            <div class="alerta-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($mensaje !== ""): ?>
            <div class="alerta-exito">
                <h3 style="margin:0 0 10px 0;"><?php echo htmlspecialchars($mensaje); ?></h3>
                <strong style="font-size: 20px; color: #1b5e20;"><?php echo $resumen_pago; ?></strong>
            </div>
        <?php endif; ?>

        <form action="agregar_persona.php" method="POST">
            <div class="fila-campos">
                <div class="campo"><label>Nombre:</label><input type="text" name="nombre" required /></div>
                <div class="campo"><label>Apellido:</label><input type="text" name="apellido" required /></div>
            </div>
            <div class="fila-campos">
                <div class="campo"><label>Ciudad:</label><input type="text" name="ciudad" placeholder="Ej: Valparaiso" required /></div>
                <div class="campo"><label>Categoría/Grupo:</label><input type="text" name="categoria" placeholder="Ej: Grupo A" required /></div>
            </div>
            <div class="campo">
                <label>Números que desea comprar:</label>
                <input type="text" name="numeros" placeholder="Ej: 14, 25, 33" required />
            </div>
            <button type="submit" class="btn">Registrar Venta</button>
        </form>
        
        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>