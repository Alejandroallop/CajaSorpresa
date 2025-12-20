<?php
session_start();
if (!isset($_SESSION['usuario_logueado'])) { header("Location: login.php"); exit; }

// Ruta al archivo de logs (ajusta si tu archivo se llama diferente)
$archivo_log = '../../logs/app.log';
$contenido = "💤 No hay actividad registrada.";

if (file_exists($archivo_log)) {
    $leido = file_get_contents($archivo_log);
    if (!empty($leido)) $contenido = $leido;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Logs del Sistema</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .console {
            background: black; color: #0f0; font-family: monospace;
            padding: 20px; height: 400px; overflow-y: auto;
            border: 2px solid #333; text-align: left; white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="app-container" style="max-width: 900px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
            <h1>📠 Logs del Sistema</h1>
            <a href="index.php" class="btn-cat">Volver</a>
        </div>
        
        <div class="console" id="caja-logs"><?= htmlspecialchars($contenido) ?></div>
        
        <script>
            // Bajar el scroll automáticamente al final
            var c = document.getElementById("caja-logs");
            c.scrollTop = c.scrollHeight;
        </script>
    </div>
</body>
</html>