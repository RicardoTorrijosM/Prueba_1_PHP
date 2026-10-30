<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$ruta_json = '../api_rest/participantes.json';
$ganador = null;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (file_exists($ruta_json)) {
        $participantes = json_decode(file_get_contents($ruta_json), true);
        if (count($participantes) > 0) {
            $indice = array_rand($participantes);
            $ganador = $participantes[$indice];
        } else {
            $error = "No hay personas inscritas aún.";
        }
    } else {
        $error = "El archivo de participantes no existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ruleta de Sorteo</title>
    <style>
        .contenedor { max-width: 600px; margin: 30px auto; padding: 24px; background: #f5f5f5; border-radius: 16px; font-family: Arial, sans-serif; box-shadow: 0 4px 15px rgba(0,0,0,.10); text-align: center; }
        .btn-sortear { padding: 15px 20px; background: #ff9800; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; font-size: 20px; text-transform: uppercase; transition: 0.2s; }
        .btn-sortear:hover { background: #f57c00; transform: scale(1.02); }
        #ruleta-animacion { margin-top: 30px; padding: 40px; background: #222; color: #fff; border-radius: 12px; }
        #numero-girando { font-size: 60px; font-weight: bold; color: #ffeb3b; margin: 10px 0; }
        #resultado-ganador { margin-top: 30px; padding: 30px; background: #4caf50; color: white; border-radius: 12px; display: none; }
        .volver { display: inline-block; margin-top: 20px; text-decoration: none; color: #1976d2; font-weight: bold; }
        .error { color: #d32f2f; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>🎰 Gran Sorteo de la Rifa 🎰</h2>

        <?php if ($error !== ""): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="ruleta.php" method="POST">
            <button type="submit" class="btn-sortear">¡Girar Ruleta!</button>
        </form>

        <?php if ($ganador): ?>
            <!-- Animación de tensión -->
            <div id="ruleta-animacion">
                <h2>Revolviendo los números...</h2>
                <div id="numero-girando">00</div>
            </div>

            <!-- Pantalla final del Ganador -->
            <div id="resultado-ganador">
                <h1>🎉 ¡TENEMOS UN GANADOR! 🎉</h1>
                <div style="font-size: 70px; font-weight: bold; margin: 15px 0;">N° <?php echo $ganador['numero_rifa']; ?></div>
                <h2>Felicidades a <?php echo htmlspecialchars($ganador['nombre'] . ' ' . $ganador['apellido']); ?></h2>
            </div>

            <script>
                // Script para crear el efecto de la ruleta girando
                let contador = 0;
                let elemNumero = document.getElementById('numero-girando');
                let intervalo = setInterval(() => {
                    // Muestra números aleatorios rápidamente
                    elemNumero.innerText = Math.floor(Math.random() * 100);
                    contador++;
                    
                    // A los 2.5 segundos se detiene y muestra el bloque verde
                    if(contador > 25) { 
                        clearInterval(intervalo);
                        document.getElementById('ruleta-animacion').style.display = 'none';
                        document.getElementById('resultado-ganador').style.display = 'block';
                    }
                }, 100);
            </script>
        <?php endif; ?>

        <br>
        <a class="volver" href="panel.php">← Volver al Panel</a>
    </div>
</body>
</html>