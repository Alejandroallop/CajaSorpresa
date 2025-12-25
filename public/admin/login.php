<?php
session_start();

// CREDENCIALES (Simples para la práctica)
$usuario_correcto = "admin";
$pass_correcta = "1234";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    if ($user === $usuario_correcto && $pass === $pass_correcta) {
        $_SESSION['usuario_logueado'] = true;
        header("Location: index.php"); // Al panel principal
        exit;
    } else {
        $error = "❌ Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body { background: #0f3460; } /* Fondo diferente para diferenciar */
        .login-box {
            background: rgba(0,0,0,0.5);
            padding: 40px;
            border-radius: 10px;
            margin-top: 100px;
        }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: none; }
        .btn-login { background: #e94560; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="login-box">
            <h2>🔒 Top Secret</h2>
            <?php if($error): ?><p style="color: red"><?= $error ?></p><?php endif; ?>
            <form method="POST">
                <input type="text" name="user" placeholder="Usuario" required>
                <input type="password" name="pass" placeholder="Contraseña" required>
                <button type="submit" class="btn-login">ENTRAR</button>
            </form>
            <br>
            <a href="../index.php" style="color: #ccc; text-decoration: none;">← Volver a la web</a>
        </div>
    </div>
</body>
</html>