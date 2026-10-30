<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

// 1. Participantes predefinidos obligatorios por la pauta
$participantes = [
    [
        "nombre" => "Universidad",
        "apellido" => "Playa Ancha",
        "ciudad" => "Valparaiso",
        "categoria" => "Grupo A"
    ],
    [
        "nombre" => "Juan",
        "apellido" => "Perez",
        "ciudad" => "Vina del Mar",
        "categoria" => "Grupo B"
    ],
    [
        "nombre" => "Maria",
        "apellido" => "Gonzalez",
        "ciudad" => "Quilpue",
        "categoria" => "Grupo A"
    ],
    [
        "nombre" => "Pedro",
        "apellido" => "Ramirez",
        "ciudad" => "Valparaiso",
        "categoria" => "Grupo C"
    ]
];

// 2. Sumar los participantes agregados dinámicamente en el JSON
$ruta_json = '../api_rest/participantes.json';
if (file_exists($ruta_json)) {
    $participantes_json = json_decode(file_get_contents($ruta_json), true);
    if (is_array($participantes_json)) {
        foreach ($participantes_json as $p) {
            $participantes[] = [
                "nombre" => trim($p['nombre'] ?? ''),
                "apellido" => trim($p['apellido'] ?? ''),
                "ciudad" => trim($p['ciudad'] ?? ''),
                "categoria" => trim($p['categoria'] ?? '')
            ];
        }
    }
}

$resultado = "";

if (isset($_GET['nombre'], $_GET['apellido'], $_GET['ciudad'], $_GET['categoria'])) {
    $nombre = trim($_GET['nombre']);
    $apellido = trim($_GET['apellido']);
    $ciudad = trim($_GET['ciudad']);
    $categoria = trim($_GET['categoria']);

    $encontrado = false;
    foreach ($participantes as $p) {
        if (
            strcasecmp($p['nombre'], $nombre) === 0 &&
            strcasecmp($p['apellido'], $apellido) === 0 &&
            strcasecmp($p['ciudad'], $ciudad) === 0 &&
            strcasecmp($p['categoria'], $categoria) === 0
        ) {
            $encontrado = true;
            break;
        }
    }

    if ($encontrado) {
        $resultado = "Participante encontrado";
    } else {
        $resultado = "Participante no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda de Participantes</title>
    <style>
        .contenedor { max-width: 500px; margin: 30px auto; padding: 24px; background: #f5f5f5; border-radius: 16px; font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10); }
        .campo { margin-bottom: 12px; }
        .campo label { display: block; font-weight: bold; margin-bottom: 4px; }
        .campo input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 6px;}
        .btn { padding: 12px 18px; background: #1976d2; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px;}
        .btn:hover { background: #0d47a1; }
        .volver { display: inline-block; margin-top: 15px; text-decoration: none; color: #1976d2; font-weight: bold; }
        .resultado { background: #fff; padding: 15px; margin-top: 20px; border-radius: 8px; text-align: center; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Búsqueda de Participantes</h2>
        <form action="buscar.php" method="GET">
            <div class="campo">
                <label>Nombre:</label>
                <input type="text" name="nombre" required value="<?php echo htmlspecialchars($_GET['nombre'] ?? ''); ?>" />
            </div>
            <div class="campo">
                <label>Apellido:</label>
                <input type="text" name="apellido" required value="<?php echo htmlspecialchars($_GET['apellido'] ?? ''); ?>" />
            </div>
            <div class="campo">
                <label>Ciudad:</label>
                <input type="text" name="ciudad" required value="<?php echo htmlspecialchars($_GET['ciudad'] ?? ''); ?>" />
            </div>
            <div class="campo">
                <label>Categoría (Grupo):</label>
                <input type="text" name="categoria" required value="<?php echo htmlspecialchars($_GET['categoria'] ?? ''); ?>" />
            </div>
            <button type="submit" class="btn">Buscar Participante</button>
        </form>

        <?php if ($resultado !== ""): ?>
            <div class="resultado">
                <h3 style="margin: 0; color: <?php echo ($resultado === "Participante encontrado") ? '#2e7d32' : '#c62828'; ?>;">
                    <?php echo $resultado; ?>
                </h3>
            </div>
        <?php endif; ?>

        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>