<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$ruta_json = '../api_rest/participantes.json';
$precio_unitario = 2000;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (file_exists($ruta_json)) {
        $participantes = json_decode(file_get_contents($ruta_json), true);
        $nuevos_participantes = [];
        
        // Eliminar un solo número específico
        if (isset($_POST['eliminar_id'])) {
            $id_eliminar = (int)$_POST['eliminar_id'];
            foreach ($participantes as $p) {
                if ($p['id'] !== $id_eliminar) {
                    $nuevos_participantes[] = $p;
                }
            }
        }
        // Eliminar a la persona completa
        elseif (isset($_POST['eliminar_ids'])) {
            $ids_eliminar = explode(',', $_POST['eliminar_ids']);
            foreach ($participantes as $p) {
                if (!in_array($p['id'], $ids_eliminar)) {
                    $nuevos_participantes[] = $p;
                }
            }
        }
        
        // Guardar cambios
        if (isset($_POST['eliminar_id']) || isset($_POST['eliminar_ids'])) {
            file_put_contents($ruta_json, json_encode($nuevos_participantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header("Location: listado_participantes.php");
            exit();
        }
    }
}

// Cargar listado desde el JSON
$participantes = file_exists($ruta_json) ? json_decode(file_get_contents($ruta_json), true) : [];
$total_numeros_vendidos = count($participantes);
$recaudacion_total = $total_numeros_vendidos * $precio_unitario;

// AGRUPAR PARTICIPANTES
$personas_agrupadas = [];
foreach ($participantes as $p) {
    $clave = strtolower(trim($p['nombre'])) . '|' . strtolower(trim($p['apellido']));
    
    if (!isset($personas_agrupadas[$clave])) {
        $personas_agrupadas[$clave] = [
            'nombre' => trim($p['nombre']),
            'apellido' => trim($p['apellido']),
            // Extraemos ciudad y categoría. Si el registro es viejo y no lo tiene, ponemos "-"
            'ciudad' => trim($p['ciudad'] ?? '-'),
            'categoria' => trim($p['categoria'] ?? '-'),
            'tickets' => [], 
            'ids' => []      
        ];
    }
    
    $personas_agrupadas[$clave]['tickets'][] = [
        'id' => $p['id'],
        'numero' => $p['numero_rifa']
    ];
    $personas_agrupadas[$clave]['ids'][] = $p['id'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Ventas</title>
    <style>
        .contenedor { max-width: 950px; margin: 30px auto; padding: 24px; background: #f5f5f5; border-radius: 16px; font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10); }
        .resumen { background: #1976d2; color: white; padding: 20px; border-radius: 12px; display: flex; justify-content: space-around; margin-bottom: 25px; text-align: center;}
        .resumen div span { display: block; font-size: 28px; font-weight: bold; margin-top: 5px;}
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #333; color: white; }
        
        .fila-participante .form-eliminar { opacity: 0; transition: opacity 0.3s ease; }
        .fila-participante:hover { background-color: #f1f8e9; }
        .fila-participante:hover .form-eliminar { opacity: 1; }
        
        .btn-eliminar-todos { background: #d32f2f; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; font-size: 12px;}
        .btn-eliminar-todos:hover { background: #b71c1c; }
        
        .btn-numero {
            background: #4caf50; color: white; border: none; padding: 5px 10px; border-radius: 4px;
            font-weight: bold; cursor: pointer; margin: 2px; font-size: 14px; transition: 0.2s;
            display: inline-flex; align-items: center; justify-content: center; min-width: 35px;
        }
        .btn-numero .icono-x { display: none; margin-left: 5px; font-size: 12px; }
        .btn-numero:hover { background: #d32f2f; } 
        .btn-numero:hover .icono-x { display: inline; } 
        .btn-numero:hover .texto-num { text-decoration: line-through; } 

        .volver { display: inline-block; margin-top: 20px; text-decoration: none; color: #1976d2; font-weight: bold; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>📊 Control de Ventas</h2>

        <div class="resumen">
            <div>Números Vendidos <span><?php echo $total_numeros_vendidos; ?></span></div>
            <div>Recaudación Total <span>$<?php echo number_format($recaudacion_total, 0, ',', '.'); ?></span></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Ciudad</th>
                    <th>Grupo</th>
                    <th>Números (Clic para borrar uno)</th>
                    <th style="width: 120px; text-align:center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($personas_agrupadas)): ?>
                    <tr><td colspan="6" style="text-align: center;">No hay números vendidos aún.</td></tr>
                <?php else: ?>
                    <?php foreach ($personas_agrupadas as $grupo): ?>
                        <tr class="fila-participante">
                            <td><?php echo htmlspecialchars(ucfirst($grupo['nombre'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($grupo['apellido'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($grupo['ciudad'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($grupo['categoria'])); ?></td>
                            <td>
                                <?php foreach ($grupo['tickets'] as $ticket): ?>
                                    <form action="listado_participantes.php" method="POST" style="display:inline-block; margin: 0;">
                                        <input type="hidden" name="eliminar_id" value="<?php echo $ticket['id']; ?>">
                                        <button type="submit" class="btn-numero" title="Eliminar número <?php echo $ticket['numero']; ?>">
                                            <span class="texto-num"><?php echo $ticket['numero']; ?></span>
                                            <span class="icono-x">✖</span>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <form action="listado_participantes.php" method="POST" class="form-eliminar" style="margin:0;">
                                    <input type="hidden" name="eliminar_ids" value="<?php echo implode(',', $grupo['ids']); ?>">
                                    <button type="submit" class="btn-eliminar-todos" title="Borrar todos los números de esta persona">Borrar Persona</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>