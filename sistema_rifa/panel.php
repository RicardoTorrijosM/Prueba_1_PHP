<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <style>
        .menu-opciones {
            max-width: 900px; margin: 30px auto; padding: 24px;
            background: #f5f5f5; border-radius: 16px;
            font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10);
        }
        .menu-opciones h2, h1 { text-align: center; color: #222; font-family: Arial, sans-serif; }
        .botones { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .boton-menu {
            display: inline-block; padding: 12px 22px; border-radius: 8px;
            background: #1976d2; color: white; text-decoration: none;
            font-weight: bold; transition: .2s;
        }
        .boton-menu:hover { background: #0d47a1; transform: translateY(-2px); }
        .boton-salir { background: #d32f2f; }
        .boton-salir:hover { background: #9a0007; }
    </style>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>
    <div class="menu-opciones">
        <h2>Menú de Administración de Rifa</h2>
        <div class="botones">
            <a class="boton-menu" href="buscar.php">Buscar Participantes</a>
            <a class="boton-menu" href="subir_comprobante.php">Subir Comprobante</a>
            <a class="boton-menu" href="archivos.php">Ver Archivos Cargados</a>
            <a class="boton-menu" style="background: #4caf50;" href="agregar_persona.php">Agregar Persona</a>
            <a class="boton-menu" style="background: #ff9800;" href="ruleta.php">Ruleta de Sorteo</a>
            <a class="boton-menu" style="background: #333;" href="listado_participantes.php">Ver Listado y Ventas</a>
            <a class="boton-menu boton-salir" href="salir.php">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>