<?php
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha_usuario = $_POST['captcha'] ?? '';

    if (
        $user === "userupla" && 
        $password === "upla123" && 
        isset($_SESSION['captcha_resultado']) && 
        (int)$captcha_usuario === $_SESSION['captcha_resultado']
    ) {
        $_SESSION['usuario'] = $user;
        $_SESSION['id'] = rand(1, 5000);
        header("Location: panel.php");
        exit();
    } else {
        $error = "ERROR EN USUARIO, CONTRASEÑA O CAPTCHA INCORRECTO";
    }
}

// Generar nuevo CAPTCHA en cada carga o intento fallido
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$_SESSION['captcha_resultado'] = $num1 + $num2;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Rifa</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eaeff2; }
        .caja-login {
            max-width: 400px; margin: 60px auto; padding: 24px;
            background: #f5f5f5; border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,.10);
        }
        .caja-login h2 { text-align: center; color: #222; }
        .campo { margin-bottom: 15px; }
        .campo label { display: block; font-weight: bold; margin-bottom: 5px; }
        .campo input { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn {
            width: 100%; padding: 12px; background: #1976d2; color: white;
            border: none; border-radius: 8px; font-weight: bold; cursor: pointer;
        }
        .btn:hover { background: #0d47a1; }
        .error { color: #d32f2f; text-align: center; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="caja-login">
        <h2>Iniciar Sesión</h2>
        <form action="index.php" method="POST">
            <div class="campo">
                <label>Usuario:</label>
                <input type="text" name="usuario" required />
            </div>
            <div class="campo">
                <label>Password:</label>
                <input type="password" name="password" required />
            </div>
            <div class="campo">
                <label>CAPTCHA: ¿Cuánto es <?php echo "$num1 + $num2"; ?>?</label>
                <input type="number" name="captcha" required />
            </div>
            <input type="submit" class="btn" value="Ingresar" />
        </form>
        <?php if ($error !== ""): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>